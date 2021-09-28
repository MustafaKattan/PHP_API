<?php
class TemplateHandler{
    private $templateName = "";
    private $templateSegments = [];

    public function __construct($templateName){
        $this->templateName = $templateName;
    }
    public function load(){
        $result = true;
        // check if template file exists
        if(is_file('../include/templates/'.$this->templateName.'.tpl')){
            $templateSegments = json_decode(file_get_contents('../include/templates/'.$this->templateName.'.tpl'), true);
            foreach($templateSegments as $segment){
                if(is_file('../include/templates/segments/'.$segment.'_tpl_segment.html')){
                    $this->templateSegments[$segment] = file_get_contents('../include/templates/segments/'.$segment.'_tpl_segment.html');
                    //Check for repeat blocks
                    preg_match_all("/(%%[\w-]*%%)/", $this->templateSegments[$segment], $matches);
                    foreach($matches[1] as $placeholder){
                        if(substr($placeholder, 0, 6) == "%%Arr_"){
                            $repeatBlockName = strtolower(substr($placeholder, 6, strlen($placeholder) - 8));
                            if(is_file('../include/templates/segments/repeat_blocks/'.$repeatBlockName.'_repeat_vertical_tpl.html')){
                                $this->templateSegments['repeat_blocks_vertical'][$repeatBlockName] =
                                    file_get_contents('../include/templates/segments/repeat_blocks/'.$repeatBlockName.'_repeat_vertical_tpl.html');
                            } elseif(is_file('../include/templates/segments/repeat_blocks/'.$repeatBlockName.'_repeat_horizontal_tpl.html')) {
                                $this->templateSegments['repeat_blocks_horizontal'][$repeatBlockName] =
                                    file_get_contents('../include/templates/segments/repeat_blocks/'.$repeatBlockName.'_repeat_horizontal_tpl.html');
                            }
                        }
                    }
                } else {
                    $result = false;
                }
            }
        } else {
            $result = false;
        }
        return $result;
    }
    public function output(){
        return $this->templateSegments;
    }

}
