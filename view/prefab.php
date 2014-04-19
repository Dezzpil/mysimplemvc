<?php
namespace msmvc;

/**
 * Description of view_prefab
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class view_prefab {

    static public function forge($name, $data) {
        return new self($name, $data);
    }


    protected $name;
    protected $data;

    private function __construct($name, $data) {
        $this->name = $name;
        $this->data = $data;
    }

    function getName() {
        return $this->name;
    }

    function getData() {
        return $this->data;
    }


    protected $asset;
    
    function addAsset(view_asset $asset) {
        $this->asset = $asset;
    }
    
    function getAsset() {
        return $this->asset;
    }


    protected $prefabs = array();

    
    function includePrefab($var_name, view_prefab $prefab) {

        if (array_key_exists($var_name, $this->prefabs) !== false) {

            // make array
            if ( ! is_array($this->prefabs[$var_name])) {
                $prevPrefab = $this->prefabs[$var_name];
                $this->prefabs[$var_name] = array();
                $this->prefabs[$var_name][] = $prevPrefab;
            }

            $this->prefabs[$var_name][] = $prefab;

        } else {

            $this->prefabs[$var_name] = $prefab;

        }
    }
    
    function getPrefabs() {
        return $this->prefabs;
    }
}
?>