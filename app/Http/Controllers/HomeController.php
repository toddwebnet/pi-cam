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
        $minutes = ($request->input('m')) ? $request->input('m') : 24 * 60;
        if ($request->input('e') === null) {
            $endDate = Carbon::create();
        } else {
            $endDate = Carbon::createFromTime(strtotime($request->input('e')));
        }
        if ($request->input('2') === null) {
            $startDate = carbon::createFromTimestamp($endDate->timestamp);
            $startDate->addMinutes(0 - $minutes);
        } else {
            $startDate = Carbon::createFromTime(strtotime($request->input('2')));
        }

        $files = json_encode($this->getFiles($startDate, $endDate));

        $data = [
            'files' => $files,
            'interval' => $interval,
        ];
        return view('index', $data);
    }

    public function last()
    {
        $request = request();
        $n = ($request->input('n')) ? $request->input('n') : 50;
        $interval = ($request->input('i')) ? $request->input('i') : '100';

        $files = $this->getLastFile($n);
        if($request->input('rev')==1){
            rsort($files);
        }

        if($request->input('s')){
            $files = array_slice($files, $request->input('s'));
        }

        $data = [
            'files' => json_encode($files),
            'interval' => $interval,
        ];
        return view('index', $data);
    }

    private function getLastFile($numEntries)
    {
        $rootPath = realpath(app_path() . '/../public');
        $path = '/images';
        $files = [];
        foreach (scandir($rootPath . $path) as $file) {
            if (in_array($file, ['.', '..', 'index.php'])) {
                continue;
            }
            $files[] = $file;
        }
        $year = max($files);

        $files = [];
        foreach (scandir($rootPath . $path . '/' . $year) as $file) {
            if (in_array($file, ['.', '..', 'index.php'])) {
                continue;
            }
            $files[] = $file;
        }
        $month = max($files);
        foreach (scandir($rootPath . $path . '/' . $year . '/' . $month) as $file) {
            if (in_array($file, ['.', '..', 'index.php'])) {
                continue;
            }
            $files[] = $file;
        }
        $day = max($files);

        $files = [];
        foreach (scandir($rootPath . $path . '/' . $year . '/' . $month . '/' . $day) as $file) {
            if (in_array($file, ['.', '..', 'index.php'])) {
                continue;
            }
            $files[] = $file;
        }
        rsort($files);

        $rfiles = [];
        foreach (array_slice($files, 0, $numEntries) as $file) {
            $rfiles[] = $path . '/' . $year . '/' . $month . '/' . $day . '/' . $file;
        }
        return $rfiles;
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
            foreach (scandir(realpath(app_path() . '/../public') . $folder) as $file) {

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
