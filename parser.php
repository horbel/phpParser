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
            $posBreak = strpos($tempName, ' от ');
            $newName = substr($tempName, 0, $posBreak);
            $this->notes[$i]->name = $newName;

        }
        for($i=0; $i<25/*count($div_tour_info)*/; $i++){
            $text =  $div_tour_info[$i]->plaintext;

            $separatedText = preg_split("/([\$]|евро|рублей).*/", $text, -1,  PREG_SPLIT_NO_EMPTY| PREG_SPLIT_DELIM_CAPTURE);
            preg_match("/(?<=(Tags:\s)).*/", $text, $this->notes[$i]->tags );
            preg_match("/(?<=(В стоимость включено:\s)).*(?=(\sДополнительно|Tags))/", $text, $this->notes[$i]->includedInTour);

            preg_match("/(?<=(Дополнительно:\s)).*(?=(Tags))/", $text, $this->notes[$i]->additionally);
            preg_match("/(?<=(Вылет\s))([0-9]{1,2}\.[0-9]{1,2}(\,\s)*)+/", $text, $this->notes[$i]->departureDate);

            for ($j = 0; $j<count($separatedText); $j++){

                $temp = array();
                if(preg_match("/.*[1-5]\s*[*](?!\))/", $separatedText[$j], $temp)){
                    array_push($this->notes[$i]->hotelNames, $temp);
                }

                if(preg_match("/(?<=([*],)).*/", $separatedText[$j], $temp)){
                    $temp[0].=$separatedText[$j+1]; //добавляем знак валюты. ОН лежит в следующей строке
                    array_push($this->notes[$i]->conditionsAndPrice, $temp);
                }

            }

        }
    }

    public function showInfo(){
        foreach ($this->notes as $note){
            echo 'Название тура : '.$note->name.'<br>';
            echo 'Теги : ';
            echo $note->tags[0].'<br>';
            echo 'Включено в тур : ';
            echo $note->includedInTour[0].'<br>';
            if($note->departureDate[0] != ''){
                echo 'Дата вылета : '.$note->departureDate[0].'<br>';
            }
            if($note->additionally[0] != ''){
                echo 'Доплнительно оплачивается : '.$note->additionally[0].'<br>';
            }

            echo '<ul>Отели :';
            $step=0;
            foreach ($note->hotelNames as $hotel){

                echo '<li>'.$hotel[0].' | .'.$note->conditionsAndPrice[++$step][0].'</li>';
            }
            echo '</ul>';

//            echo '<ul>Условия и цена :';
//            foreach ($note->conditionsAndPrice as $con){
//                echo '<li>'.$con[0].'</li>';
//            }
//            echo '</ul>';

            echo '<hr>';
        }
    }




}
class Note{
    public $name;  //название тура
    public $departureDate; //дата вылета
    public $conditionsAndPrice; // тип питания, количество ночей и стоимость
    public $hotelNames; //название отелей
    public $tags;  //список тегов
    public $additionally;   //дополнительно оплачивается
    public $includedInTour;  //включено в стоимость тура
    function __construct()
    {
        $this->hotelNames = array();
        $this->tags = array();
        $this->includedInTour = array();
        $this->departureDate = array();
        $this->conditionsAndPrice = array();

    }

    public $typeOfFood;
    public $stars;

}
$s = new Source();

$s->TryParse($html);
$s->showInfo();


?>