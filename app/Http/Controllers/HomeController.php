<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{

    public function index()
    {

        $request = request();

        $interval = ($request->input('i')) ? $request->input('i') : '100';
        $hours = ($request->input('h')) ? $request->input('h') : '24';
        if ($request->input('e') === null) {
            $endDate = Carbon::create();
        } else {
            $endDate = Carbon::createFromTime(strtotime($request->input('e')));
        }
        if ($request->input('2') === null) {
            $startDate = carbon::createFromTimestamp($endDate->timestamp);
            $startDate->addHours(0 - $hours);
        } else {
            $startDate = Carbon::createFromTime(strtotime($request->input('2')));
        }

        $data = [
            'files' => json_encode($this->getFiles($startDate, $endDate)),
            'interval' => $interval,
        ];
        return view('index', $data);
    }

    private function getFiles(Carbon $startDate, Carbon $endDate)
    {
        return $this->getFilesFromFolders(
            $this->getFolders($startDate, $endDate),
            $startDate, $endDate
        );
    }

    private function getFolders(Carbon $startDate, Carbon $endDate)
    {
        $folders = [];
        $workDate = Carbon::createFromTimestamp($startDate->timestamp);
        do {
            list($year, $month, $day) = explode('-', $workDate->toDateString());
            $folders[] = "/images/{$year}/{$month}/{$day}";
            $workDate->addDay(1);
        } while ($workDate->timestamp <= $endDate->timestamp);
        return $folders;
    }

    private function getFilesFromFolders(array $folders, Carbon $startDate, Carbon $endDate)
    {

        $files = [];
        $startDate = $startDate->timestamp;
        $endDate = $endDate->timestamp;
        foreach ($folders as $folder) {
            foreach (scandir($rootPath = realpath(app_path() . '/../public') . $folder) as $file) {

                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                $info = (object)pathinfo($file);
                if ($info->extension != 'jpg') {
                    continue;
                }
                if (is_numeric($info->filename) && $info->filename >= $startDate && $info->filename < $endDate) {
                    $files[] = $folder . '/' . $info->basename;
                }
            }
        }
        return $files;
    }

}
