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
        for($i=0; $i<count($div_titles); $i++){
            $tempName = $div_titles[$i]->innertext;
            $posBreak = strpos($tempName, ' от ');
            $newName = substr($tempName, 0, $posBreak);
            $this->notes[$i]->name = $newName;
        }
        for($i=0; i<count($div_tour_info); $i++){
            $text =  $div_tour_info[$i]->plaintext;
            //$separatedText = preg_split("/\$/", $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
            $separatedText = explode(',', $text);

            $this->notes[$i]->hotelNames[] =   $separatedText[0];
            for ($j = 0; $j<count($separatedText); $j++){

                $arr = str_split($separatedText[$j]);
                //$temp = preg_grep("/*$/", $arr);


                //GET ALLMOST EVERY FIELDs HERE


                //echo $separatedText[$j];
                //echo '<br>';
            }
           // echo '<hr>';


        }
    }

    public function showInfo(){
        foreach ($this->notes as $note){
            echo 'Название тура : '.$note->name;
            echo 'Отели : <br>';
            foreach ($note->hotelNames as $hName){
                echo '<p>'.$hName;
            }
            echo '<br>';
        }
    }




}
class Note{
    public $name;
    public $departureCity;
    public $startDate;
    public $nightsAndPrice;
    public $typeOLocation;
    public $hotelNames;  //array
    function __construct()
    {
        $this->hotelNames = array();
    }

    public $typeOfFood;
    public $stars;

}
$s = new Source();
$s->TryParse($html);
$s->showInfo();


/*foreach ($div_titles as $el){
    echo $el->innertext;
    echo '<br>';
}
 */
?>