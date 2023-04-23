<?php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MyArrayExport;
use Maatwebsite\Excel\Concerns\FromCollection;

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyArrayExport implements FromCollection
{
    protected $myArray;

    public function __construct(array $myArray)
    {
        $this->myArray = $myArray;
    }

    public function collection()
    {
        return collect($this->myArray);
    }
}

function exportArray($array)
{
    $myArray = $array;// El array que deseas exportar

    return Excel::download(new MyArrayExport($myArray), 'my_array.xlsx');
}
