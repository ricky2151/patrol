<?php
namespace App\Services\Implementations;

use App\Services\Contracts\UserServiceContract;
use App\Repositories\Contracts\UserRepositoryContract as UserRepo;
use App\Repositories\Contracts\ShiftRepositoryContract as ShiftRepo;
use App\Repositories\Contracts\PhotoRepositoryContract as PhotoRepo;
use App\Repositories\Contracts\HistoryRepositoryContract as HistoryRepo;
use App\Repositories\Contracts\AuthRepositoryContract as AuthRepo;
use Carbon\Carbon;
use App\Exceptions\SuspiciousInputException;
use Illuminate\Support\Arr;

class UserServiceImplementation implements UserServiceContract {
    protected $userRepo;
    protected $shiftRepo;
    protected $photoRepo;
    protected $historyRepo;
    protected $authRepo;

    public function __construct(UserRepo $userRepo, ShiftRepo $shiftRepo, PhotoRepo $photoRepo, HistoryRepo $historyRepo, AuthRepo $authRepo)
    {
        $this->userRepo = $userRepo;
        $this->shiftRepo = $shiftRepo;
        $this->photoRepo = $photoRepo;
        $this->historyRepo = $historyRepo;
        $this->authRepo = $authRepo;
    }

    public function getShiftsThatCanBeScanned() {
        return $this->userRepo->getShiftsThatCanBeScanned();
    }

    public function viewMyHistoryScan($id) {
        return $this->userRepo->viewMyHistoryScan($id);
    }

    public function submitScan($message, $statusNodeId, $id, $dateTimeNow, $photos) {

        //1. split dateTimeNow to dateNow and timeNow, also get dateYesterday
        $dateTimeNowInCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeNow, 'Asia/Jakarta');
        $timeNow = $dateTimeNowInCarbon->format("H:i:s");
        $dateNow = $dateTimeNowInCarbon->format("Y-m-d");
        $dateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($dateNow)));

        //2. get shift data
        $shift = $this->shiftRepo->find($id);

        //3. get user's that shift
        $idUserThisShift = $shift['user_id'];

        //4. if user's that shift = user's request, then process scan data
        $thisUser = $this->authRepo->isLogin();
        if($thisUser['user']['id']== $idUserThisShift) {

            //5. check wether datetime now is correct with shift datetime
            $timeShift = $this->shiftRepo->getTime($id);
            $startTimeShift = $timeShift['start'];
            $endTimeShift = $timeShift['end'];
            $dateShift = $shift['date'];
            if(checkRangeTimeShift($timeNow, $dateNow, $dateYesterday, $dateShift, $startTimeShift, $endTimeShift, "between")) {
                
                //6. check wether user send photos or not
                if(count($photos) > 0)
                {
                    
                    //7. check wether all photo time is correct with shift datetime
                    foreach($photos as $photo)
                    {
                        $photoDate = substr($photo['photo_time'],0, 10);
                        $photoTime = substr($photo['photo_time'],11, 8);
                        $photoDateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($photoDate)));
                        if(!checkRangeTimeShift($photoTime, $photoDate, $photoDateYesterday, $dateShift, $startTimeShift, $endTimeShift, "between")) {
                            throw new SuspiciousInputException("there is an incorrect photo_time data");
                        }
                    }
                    
                    //8. prepare history data before store to database
                    $roomName = $this->shiftRepo->getRoom($id)['name'];
                    $tempShift = [];
                    $tempShift['shift_id'] = $id;
                    $tempShift['status_node_id'] = $statusNodeId;
                    $tempShift['scan_time'] = $timeNow;
                    isset($message) ? $tempShift['message'] = $message : $tempShift['message'] = '';
                   
                    //9. save each photo in photos to storage
                    $photosSaved = [];
                    $indexPhoto = 0;
                    foreach($photos as $photo)
                    {
                        $path = '';
                        $folder = 'photos';
                        $image = $photo['file'];
                        $photoTime = $photo['photo_time'];
                        
                        //9.1. check wether image is null
                        if(!is_null($image)){
                            //make file name
                            //[photo time]-[user fullname]-[room name]-[index photo]
                            //example : 
                            //132324-ricky-RuanganPosSatpamAgape-0
                            $name = preg_replace('/[^a-zA-Z0-9-_\.]/','',$photoTime . " - " . $thisUser['user']['name'] . " - " . $roomName . " - " . $indexPhoto);
                            $name = $name . ".jpg";
                            $path = $this->photoRepo->savePhotoToStorage($image, $folder, $name);
                        }
                        else{
                            $path = $folder."/default.png";
                        }
                        $photosSaved[] = ['url' => $path, 'photo_time' => $photoTime];
                        $indexPhoto += 1;
                    }
                    
                    //10. store history to database
                    $idHistoryRepo = $this->historyRepo->store($tempShift)['id'];

                    //11. store photos data to database
                    if(count($photosSaved) > 0)
                        $this->historyRepo->insertPhotos($idHistoryRepo, $photosSaved);
                    
                    //12. return true because it's success
                    return true;
                    
                }
                else
                {
                    throw new SuspiciousInputException("no photo uploaded");
                }
            }
            else {
                throw new SuspiciousInputException("server time is not correct");
            }

        }
        else {
            throw new SuspiciousInputException("this user is not have this shift");
        }
        
    }

    public function get() {
        $role_id = $this->authRepo->isLogin()['user']['role_id'];
        if($role_id == 3) { //if user is superadmin, then return user data with role admin and guard
            //$data = $this->userRepo->allOrder('id', 'desc');
        } else { //if user is not superadmin (it should admin), then return user data with role guard
            //$data = $this->userRepo->datatable->where('role_id', 1)->orderBy('id', 'desc')->
        }
        $data = $this->userRepo->allOrder('id', 'desc');
        
        
        
        
        $data = collect($data)->map(function ($item) {
            $item = Arr::add($item, 'role_name', $item['role']['name']);
            return Arr::except($item, ['role']);
        });
        return $data->toArray();
    }

    public function getShifts($id) {
        $data = $this->userRepo->getShifts($id);
        
        $data = collect($data)->map(function ($item) {
            $item = Arr::add($item, 'room_name', $item['room']['name']);
            $item = Arr::add($item, 'time_start_end', $item['time']['start'] . " - " . $item['time']['end']);
            return Arr::except($item, ['room', 'time', 'room_id', 'time_id']);
        });
        return $data->toArray();
    }

    public function storeUserWithShifts($input) {
        //1. encrypt password
        $input["password"] = bcrypt($input["password"]);
        //2. generate master key
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $input['master_key'] = $randomString;


        //3. split to $inputUser and $inputShift
        $inputUser = $input;
        unset($inputUser['shifts']);
        $inputShift = $input['shifts'];

        //4. store user data
        $newId = $this->userRepo->store($inputUser)['id'];

        //5. store user's shifts
        if(count($inputShift) > 0) {
            $this->userRepo->insertShifts($newId, $inputShift);
        }
        return true;
    }

    public function updateUserWithShifts($input, $id) {
        //1. separate input user data and input user's shifts
        
        $inputUser = $input;
        unset($inputUser['shifts']);

        $inputShift = isset($input['shifts']) ? $input['shifts'] : [];

        //2. convert shift's to collection
        $inputShiftCollection = collect($inputShift);

        //3. get list id on inputshift variable
        //means it have 2 possibility : update shift or delete shift
        //because insert shift is not using id shift.
        $listIdShifts = $inputShiftCollection->filter(function ($value, $key) {
            return array_key_exists('id', $value);
        })->pluck('id')->toArray();
        
        //4. check wether there is suspicious input in shift data or not
        if(count($listIdShifts) > 0) {
            if(!($this->userRepo->checkHaveShifts($id, $listIdShifts))) { //just to ensure wether user have this shift or not
                throw new SuspiciousInputException("this user is not have this shift");
            }
        }

        //5. update user data
        $this->userRepo->update($inputUser, $id);

        //6. prepare new shift's user data (it can be insert shifts, update shifts, or delete shifts)
        
        $shiftsToBeInserted = $inputShiftCollection->where('type', 1)->toArray();
        $shiftsToBeUpdated = $inputShiftCollection->where('type', 0)->toArray();
        $shiftsToBeDeleted = $inputShiftCollection->where('type', -1)->toArray();

        //7. remove type key
        foreach ($shiftsToBeInserted as $key => $item) {
            unset($item['type']);
            $shiftsToBeInserted[$key] = $item;  
        }
        foreach ($shiftsToBeUpdated as $key => $item) {
            unset($item['type']);
            $shiftsToBeUpdated[$key] = $item;  
        }
        foreach ($shiftsToBeDeleted as $key => $item) {
            unset($item['type']);
            $shiftsToBeDeleted[$key] = $item;  
        }

        
        //8. insert new shift
        if($shiftsToBeInserted != null)
            
            $this->userRepo->insertShifts($id, $shiftsToBeInserted);

        //9. update new shift
        if($shiftsToBeUpdated != null)
            $this->userRepo->updateShifts($id, $shiftsToBeUpdated);

        //10. delete new shift
        if($shiftsToBeDeleted != null)
            $this->userRepo->deleteShifts($id, $shiftsToBeDeleted);

        return true;
        
    }


    public function findWithShifts($id) {
        //1. get user data with user's shifts
        $data = $this->userRepo->findWithShifts($id);

        

        //2. delete role_id key
        $data = Arr::except($data, ['role_id']);

        //3. filter shifts
        $shifts = $data['shifts'];
        $shifts = collect($shifts)->map(function($item) {
            $item['time']['name'] = $item['time']['start'] . '-' . $item['time']['end'];
            Arr::forget($item, 'time.start');
            Arr::forget($item, 'time.end');
            return Arr::except($item, ['user_id', 'room_id', 'time_id']);
        })->toArray();

        //filter user
        Arr::forget($data, 'shifts');
        $user = $data;

        return([
            'user' => $user,
            'shifts' => $shifts
        ]);

        
    }

    public function delete($id) {
        return $this->userRepo->delete($id);
    }

    
}