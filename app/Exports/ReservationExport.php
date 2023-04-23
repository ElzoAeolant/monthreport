<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReservationExport implements FromCollection,WithHeadings
{
    protected $dbReservation;

    public function __construct($dbR)
    {
       $this->dbReservation = $dbR;
    }

    public function headings():array{
        $headers = array();
        //Headers
        array_push($headers,"ID");
        array_push($headers,"Room_ID");
        array_push($headers,"Guest_Name");
        array_push($headers,"Fuente");
        array_push($headers,"Source_ID");
        array_push($headers,"Source_ID");
        array_push($headers,"Check_Out");
        array_push($headers,"Total_de_Noches");
        array_push($headers,"Index precio por noche");
        array_push($headers,"Subtotal");
        array_push($headers,"IVA");
        array_push($headers,"ISH");
        array_push($headers,"Total Tax");
        array_push($headers,"Total");
        array_push($headers,"Extras");
        array_push($headers,"Ajustes");
        array_push($headers,"Pagado");
        array_push($headers,"Comentarios");
        array_push($headers,"Adults");
        array_push($headers,"Childs");
        array_push($headers,"Status");
        array_push($headers,"Pago Total");
        array_push($headers,"Diferencia");
        array_push($headers,"Mes Anterior");
        array_push($headers,"Comentarios");
        array_push($headers,"FlowCase");
        array_push($headers,"Room Type");
        return $headers;
    }

    /*
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->dbReservation;
    }

}