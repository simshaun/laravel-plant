<?php

class Plant_Seed_Task extends Task {

    public function run($arguments)
    {
        $command = empty($arguments) || $arguments[0] === '' ? 'displayHelp' : $arguments[0];
        $arguments = array_slice($arguments, 1);

        if (method_exists($this, $command))
        {
            $this->$command($arguments);
        }
        else
        {
            $this->_growAllSeeds($command);
        }
    }

    public function displayHelp()
    {
        $this->_log('Usage :');
        $this->_log("\tplant::seed[:name]");
        $this->_log("  e.g.\tplant::seed:all");
        $this->_log("\nArguments :");
        $this->_log("\t--not=users[,comments,posts]");
    }

    /**
     * Grow all seeds that can be found.
     */
    public function all()
    {
        $this->_growAllSeeds();
    }

    /**
     * @return array of paths that we can find seeds
     */
    protected function _getSeedFolders()
    {
        $bins = array( path('app').'seeds'.DIRECTORY_SEPARATOR );
        foreach (Bundle::$bundles as $bundle)
        {
            $bins[] = Bundle::path($bundle['location']).'seeds'.DIRECTORY_SEPARATOR;
        }

        return $bins;
    }

    protected function _getSeedFiles($name = null)
    {
        $filenames = ($name ?: '*');
        $filenames = explode(',', $filenames);

        $files = array();
        foreach ($this->_getSeedFolders() as $path)
        {
            foreach ($filenames as $filename)
            {
                $files = array_merge($files, glob($path.$filename.'.php'));
            }
        }

        if (false !== $this->_config('not'))
        {
            $exclude = explode(',', $this->_config('not'));
            foreach ($files as $key => $file)
            {
                if (in_array(pathinfo($file, PATHINFO_FILENAME), $exclude))
                {
                    unset($files[$key]);
                }
            }
        }

        return $files;
    }

    protected function _getSeedObject($file)
    {
        include_once $file;

        $name = ucfirst(pathinfo($file, PATHINFO_FILENAME));
        $class = 'Seed_'.$name;
        return new $class;
    }

    /**
     * @param $seed \S2\Seed
     */
    protected function _growSeed($seed)
    {
        $this->_log('Growing '.get_class($seed));
        $seed->grow();
    }

    protected function _growAllSeeds($name = null)
    {
        $seeds = array();
        $files = $this->_getSeedFiles($name);

        if (empty($files))
        {
            $this->_log(empty($name) ? 'There are no seeds to grow.' : 'We cant find any "'.$name.'" seeds to grow.');
            return;
        }

        foreach ($files as $file)
        {
            $seeds[] = $this->_getSeedObject($file);
        }

        if (!empty($seeds))
        {
            $seeds = $this->_sortSeeds($seeds);
            foreach ($seeds as $seed)
            {
                $this->_growSeed($seed);
            }

            $this->_log('Finished!');
        }
    }

    /**
     * Sorts an array of seed objects using the returned value of
     * each seed's order() method as a basis for comparison.
     * @param array $seeds
     * @return array
     */
    protected function _sortSeeds($seeds)
    {
        usort($seeds, array($this, '_sortSeedsHelper'));
        return $seeds;
    }

    /**
     * Helper method for sorting seed objects
     * @param $a \S2\Seed
     * @param $b \S2\Seed
     * @return int
     */
    protected function _sortSeedsHelper($a, $b)
    {
        return strcmp($a->order(), $b->order());
    }

    protected function _log($str)
    {
        echo $str.PHP_EOL;
    }

    protected function _config($key)
    {
        if(isset($_SERVER['CLI'][Str::upper($key)]))
        {
            return ($_SERVER['CLI'][Str::upper($key)] == '') ? true : $_SERVER['CLI'][Str::upper($key)];
        }
        else
        {
            return false;
        }
    }

}