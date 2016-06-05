<?php
include_once 'Z:\home\localhost\www\simplehtmldom_1_5\simple_html_dom.php';

$html = file_get_html('http://zestrest.livejournal.com');

class  Source{
    public $notes; //array of objects
    function __construct()
    {
        $this->notes = array();
    }
    public function TryParse($html){
        $div_titles = $html->find('div[class=subject] a');
        $div_tour_info = $html->find('div[class=entry_text]');
        for($i=0; $i<25/*count($div_titles)*/; $i++){
            $this->notes[$i] = new Note;
            $tempName = $div_titles[$i]->innertext;
            $this->notes[$i]->name = $tempName;

        }
        for($i=0; $i<25/*count($div_tour_info)*/; $i++){
            $text =  $div_tour_info[$i]->plaintext;

            $separatedText = preg_split("/([\$]|евро|рублей)/", $text, -1,  PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            preg_match("/(?<=(Tags:\s)).*/", $text, $this->notes[$i]->tags );
            preg_match("/(?<=(В стоимость включено:\s)).*(?=(\sДополнительно|Tags))/", $text, $this->notes[$i]->includedInTour);

            preg_match("/(?<=(Дополнительно:\s)).*(?=(Tags))/", $text, $this->notes[$i]->additionally);
            preg_match("/(?<=(Вылет\s))([0-9]{1,2}\.[0-9]{1,2}(\,\s)*)+/", $text, $this->notes[$i]->departureDate);
            $count = -1;

            for ($j = 0; $j<count($separatedText); $j++){

                $temp = array();
                if(preg_match("/.*[1-5]\s*[*](?!\))/", $separatedText[$j], $temp)){
                    $this->notes[$i]->hotelInfo[++$count] = new HotelInfo;
                    array_push($this->notes[$i]->hotelInfo[$count]->hotelNames, $temp[0]);
                }
                $k=2;
                if(preg_match("/(.*[1-5]\s*[*][\s,]*(.*\)[,\s])*)(.*)/", $separatedText[$j], $temp)) {
                    $temp[3] .= $separatedText[$j + 1];
                    array_push($this->notes[$i]->hotelInfo[$count]->conditionsAndPrice, $temp[3]);
                    while (preg_match("/^[,\s](.*)$/", $separatedText[$j + $k], $temp)) {
                        $temp[1] .= $separatedText[$j + $k + 1]; //добавляем знак валюты. ОН лежит в следующей строке
                        $k = $k + 2;
                        array_push($this->notes[$i]->hotelInfo[$count]->conditionsAndPrice, $temp[1]);
                    }
                }

                }
                //echo '<hr>';
            }

        }


    public function showInfo(){
        foreach ($this->notes as $note){
            echo '<b>Название тура : </b>'.$note->name.'<br>';
            echo '<b>Теги : </b>';
            echo $note->tags[0].'<br>';
            echo '<b>Включено в тур : </b>';
            echo $note->includedInTour[0].'<br>';
            echo '<b>Город вылета : </b>';
            echo $note->departureCity.'<br>';
            if($note->departureDate[0] != ''){
                echo '<b>Дата вылета : </b>'.$note->departureDate[0].'<br>';
            }
            if($note->additionally[0] != ''){
                echo '<b>Доплнительно оплачивается : </b>'.$note->additionally[0].'<br>';
            }
            echo '<ul><b>Отели :</b>';
            for ($i=0;$i<count($note->hotelInfo); $i++){
                echo '<li>'.$note->hotelInfo[$i]->hotelNames[0].'<br>';
                for($h=0; $h<count($note->hotelInfo[$i]->conditionsAndPrice); $h++){
                    echo $note->hotelInfo[$i]->conditionsAndPrice[$h].'<br>';
                }
                echo  '</li>';
            }
            echo '</ul>';
            echo '<hr>';
        }
    }

}
class Note{
    public $name;  //название тура
    public $departureDate; //дата вылета
    public $departureCity;  
    public $hotelInfo;    
    public $tags;  //список тегов
    public $additionally;   //дополнительно оплачивается
    public $includedInTour;  //включено в стоимость тура
    function __construct()
    {
        $this->tags = array();
        $this->includedInTour = array();
        $this->departureDate = array();
        $this->departureCity = 'Москва'; //хардкод для данного источника        
        $this->hotelInfp = array();
    }
    public $typeOfFood;
    public $stars;

}
class HotelInfo{
    public $hotelName;
    public $numberOfNights;
    public $price;
    public $conditionsAndPrice; // тип питания, количество ночей и стоимость
    function __construct(){
        $this->conditionsAndPrice = array();
        $this->hotelNames = array();
    }
}
$s = new Source();

$s->TryParse($html);
$s->showInfo();


?>