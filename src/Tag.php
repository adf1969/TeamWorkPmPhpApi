<?php namespace TeamWorkPm;

class Tag extends Model
{
    public static $tagColors = array(
      '#d84640',  //Red
      '#f78234',  //Orange
      '#f4bd38',  //Mustard
      '#b1da34',  //Lime
      '#53c944',  //Green
      '#37ced0',  //Teal
      '#2f8de4',  //Blue
      '#9b7cdb',  //Purple
      '#f47fbe',  //Pink
      '#a6a6a6',  //Grey
      '#4d4d4d',  //Shadow
      '#9e6957'   //Brown
    );
    
    protected function init()
    {
        $this->fields = [
            'name'=> false,
            'color'=> false,
            'content' => false,
            'replaceExistingTags' => false,
            'removeProvidedTags' => false,
        ];
        $this->parent = 'tag';
        $this->action = 'tags';
   }

    public function get($id, $get_time = false)
    {
        $id = (int) $id;
        if ($id <= 0) {
            throw new Exception('Invalid param id');
        }
        $params = [];
        if ($get_time) {
            $params['getTime'] = (int) $get_time;
        }
        return $this->rest->get("$this->action/$id", $params);
    }


    public function getAll() {      
      return $this->rest->get("$this->action");
    }
    
    public function insert(array $data) {
      if (isset($data['tags'])) {
        $this->action = $data['action'];
        // I don't think I need this since the default parent = 'tags'
        $this->rest->getRequest()
                   ->setParent('tags');
        $data = $data['tags'];
        return $this->rest->put($this->action, $data);
      } else {
        $color = empty($data['color']) ? self::$tagColors[0] : $data['color'];
        if (!array_search($color, self::$tagColors)) {
          // Color not found in $tagColors - set it to first color
          $color = self::$tagColors[0];
        }
        $data['color'] = $color;        
        return $this->rest->post($this->action, $data);
      }            
  }

  public function update(array $data)  {
      $id = empty($data['id']) ? 0: (int) $data['id'];
      if ($id <= 0) {
          throw new Exception('Required field id');
      }
//      $this->rest->getRequest()
//                 ->setParent('tag');      
      return $this->rest->put("$this->action/$id", $data);
  }  
  
    
}