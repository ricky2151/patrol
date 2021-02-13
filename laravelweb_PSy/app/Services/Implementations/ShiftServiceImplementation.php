<?php
namespace App\Services\Implementations;

use App\Services\Contracts\ShiftServiceContract;
use App\Repositories\Contracts\StatusNodeRepositoryContract as StatusNodeRepo;
use App\Repositories\Contracts\HistoryRepositoryContract as HistoryRepo;
use App\Repositories\Contracts\ShiftRepositoryContract as ShiftRepo;
use App\Exceptions\GetDataFailedException;
use Illuminate\Support\Arr;

class ShiftServiceImplementation implements ShiftServiceContract {
    protected $statusNodeRepo, $historyRepo, $shiftRepo;

    public function __construct(ShiftRepo $shiftRepo, StatusNodeRepo $statusNodeRepo, HistoryRepo $historyRepo)
    {
        $this->statusNodeRepo = $statusNodeRepo;
        $this->historyRepo = $historyRepo;
        $this->shiftRepo = $shiftRepo;
    }

    public function getDashboardData() {
        $statusNodeData = $this->statusNodeRepo->getAll();
        if(count($statusNodeData) == 0) {
            throw new GetDataFailedException();
        } else {
            $graphData = $this->historyRepo->getGraphData();
            $currentEvent = $this->historyRepo->getCurrentEvent();
            return [
                'currentEvent' => $currentEvent,
                'graphData' => $graphData,
                'statusNodeData' => $statusNodeData,
            ];
        }
    }

    public function getHistoryScan($id) {
        $data = $this->shiftRepo->getHistoryScan($id);
        
        $data['room_name'] = $data['room']['name'];
        $data['time_name'] = $data['time']['start'] . " - " . $data['time']['end'];
        unset($data['room']);
        unset($data['time']);

        $data['histories'] = collect($data['histories'])->map(function($history) {
            $history =  Arr::add($history, 'status_node_name', $history['status_node']['name']);
            $history['photos'] = collect($history['photos'])->map(function($photo) {
                return Arr::except($photo, ['history_id']);
            })->toArray();
            return Arr::except($history, ['status_node']);
        })->toArray();
        
        return $data;
    }

    public function getShiftsNotAssign() {
        return $this->shiftRepo->getShiftsNotAssign();
    }

    public function get() {
        $data = $this->shiftRepo->getWithFormat();

        $data = collect($data)->map(function ($item) {
            $item = Arr::add($item, 'room_name', $item['room']['name']);
            $item = Arr::add($item, 'user_name', $item['user']['name']);
            $item = Arr::add($item, 'time_start_end', $item['time']['start'] . ' - ' . $item['time']['end']);
            $item['total_histories'] = intval($item['histories_count']);
            return Arr::except($item, ['user_id', 'room_id', 'time_id', 'room', 'user', 'time', 'histories_count']);
        });
        return $data->toArray();

    }

    public function getToday() {
        $data = $this->shiftRepo->getTodayWithFormat();

        $data = collect($data)->map(function ($item) {
            $item = Arr::add($item, 'room_name', $item['room']['name']);
            $item = Arr::add($item, 'user_name', $item['user']['name']);
            $item = Arr::add($item, 'time_start_end', $item['time']['start'] . ' - ' . $item['time']['end']);
            $item['total_histories'] = intval($item['histories_count']);
            return Arr::except($item, ['user_id', 'room_id', 'time_id', 'room', 'user', 'time', 'histories_count']);
        });
        return $data->toArray();
    }

    public function removeAndBackup() {
        if($this->shiftRepo->backupRun()) {
            $this->shiftRepo->removeShiftsExceptToday();
            return true;
        }
    }
}