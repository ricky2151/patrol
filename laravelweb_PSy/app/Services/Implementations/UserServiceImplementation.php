<?php
namespace App\Services\Implementations;

use App\Services\Contracts\UserServiceContract;
use App\Repositories\Contracts\UserRepositoryContract as UserRepo;
use App\Repositories\Contracts\ShiftRepositoryContract as ShiftRepo;
use App\Repositories\Contracts\PhotoRepositoryContract as PhotoRepo;
use App\Repositories\Contracts\HistoryRepositoryContract as HistoryRepo;
use App\Repositories\Contracts\AuthRepositoryContract as AuthRepo;
use App\Repositories\Contracts\StatusNodeRepositoryContract as StatusNodeRepo;
use Carbon\Carbon;
use App\Exceptions\SuspiciousInputException;

class UserServiceImplementation implements UserServiceContract {
    protected $userRepo;
    protected $shiftRepo;
    protected $photoRepo;
    protected $historyRepo;
    protected $authRepo;
    protected $statusNodeRepo;

    public function __construct(UserRepo $userRepo, ShiftRepo $shiftRepo, PhotoRepo $photoRepo, HistoryRepo $historyRepo, AuthRepo $authRepo, StatusNodeRepo $statusNodeRepo)
    {
        $this->userRepo = $userRepo;
        $this->shiftRepo = $shiftRepo;
        $this->photoRepo = $photoRepo;
        $this->historyRepo = $historyRepo;
        $this->authRepo = $authRepo;
        $this->statusNodeRepo = $statusNodeRepo;
    }

    public function getShiftsThatCanBeScanned() {
        return $this->userRepo->getShiftsThatCanBeScanned();
    }

    public function getMasterDataSubmitScan() {
        return $this->statusNodeRepo->getAll();
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
}