<?php

class Rank_Statistics_Model extends MY_Model
{

    private $defaultWebChannel;
    private $defaultAppChannel;

    public function __construct() 
    {
        parent::__construct();
        $defaultChannels = $this->config->item('defaultChannel');
        $this->defaultWebChannel = $defaultChannels['web'];
        $this->defaultAppChannel = $defaultChannels['app'];
    }
    
    private function filterFunc($str)
    {
    	return str_replace('"', '', $str);
    }

    public function deliveryAppContent($content)
    {
        $splitAry = preg_split('/\s+/', $content, NULL, PREG_SPLIT_NO_EMPTY);
        $splitAry = array_map(array($this, 'filterFunc'), $splitAry);
        $headAry = explode(',', $splitAry[0]);
        $fields = array();
        foreach ($headAry as $key)
        {
            if ($key == 'day')
            {
                $key = 'date';
            }
            if ($key == 'channel')
            {
                $key = 'channel_id';
            }
            array_push($fields, $key);
        }

        $updateFields = array_diff($fields, array('date', 'channel_id'));
        $updates = array();
        foreach ($updateFields as $uf)
        {
            array_push($updates, "$uf = VALUES($uf)");
        }
        $updateStr = implode(',', $updates);

        array_push($fields, 'created');
        $fieldStr = implode(',', $fields);

        $validChannels = $this->fetchValidChannels();
        $channelNameToId = array_flip($validChannels);
        $values = array();
        for ($ph = 1, $count = count($splitAry); $ph < $count; $ph ++)
        {
            $bodyRowAry = explode(',', $splitAry[$ph]);
            $rowHash = array_combine($headAry, $bodyRowAry);
            //根据传来的channel映射到对应的channelId上面去
            if (empty($rowHash['channel']))
            {
                $rowHash['channel'] = $this->defaultAppChannel;
            }
            elseif (in_array($rowHash['channel'], $validChannels))
            {
                $rowHash['channel'] = $channelNameToId[$rowHash['channel']];
            }
            else
            {
                $rowHash['channel'] = $channelNameToId['test'];
            }
            $value = "('" . implode("','", array_values($rowHash)) . "', NOW())";
            array_push($values, $value);
        }

        if ($values)
        {
            $valueStr = implode(',', $values);
            $sql = "INSERT cp_50bang_app ($fieldStr) VALUES $valueStr ON DUPLICATE KEY UPDATE $updateStr";
            $this->db->query($sql);
        }
    }

    private function shouldOmit($channelId)
    {
        return ((ENVIRONMENT === 'production') && $channelId <= 10000)
            OR ((ENVIRONMENT !== 'production') && $channelId > 10000);
    }

    public function deliveryWebContent($typeToStat, $date)
    {
        $channelPattern = '/cpk=(\w+)/';
        $channelIds = array(0);
        $channelToStat = array(
            0 => array(
                'browse_ip' => 0,
                'browse_pv' => 0,
                'browse_uv' => 0,
                'click_ip'  => 0,
                'click_pv'  => 0,
                'click_uv'  => 0,
            ),
        );
        $validChannels = $this->fetchValidChannels();
        $channelNameToId = array_flip($validChannels);
        foreach ($typeToStat as $prefix => $statistics)
        {
            $statAll = unserialize($statistics);
            foreach ($statAll as $statOne)
            {
                if (preg_match($channelPattern, $statOne['URL'], $matches))
                {
                    if (in_array($matches[1], $validChannels))
                    {
                        $channelId = $channelNameToId[$matches[1]];
                    }
                    elseif (in_array($matches[1], $channelNameToId))
                    {
                        $channelId = $matches[1];
                    }
                    else
                    {
                        $channelId = $this->defaultWebChannel;
                    }

                    if ($this->shouldOmit($channelId))
                    {
                        continue;
                    }
                }
                else
                {
                    $channelId = $this->defaultWebChannel;
                }

                if ( ! in_array($channelId, $channelIds))
                {
                    $channelToStat[$channelId] = array(
                        'browse_ip' => 0,
                        'browse_pv' => 0,
                        'browse_uv' => 0,
                        'click_ip'  => 0,
                        'click_pv'  => 0,
                        'click_uv'  => 0,
                    );
                    array_push($channelIds, $channelId);
                }

                $channelToStat[$channelId][$prefix . '_ip'] += $statOne['IPCount'];
                $channelToStat[$channelId][$prefix . '_uv'] += $statOne['GUIDCount'];
                $channelToStat[$channelId][$prefix . '_pv'] += $statOne['ALLCount'];
            }
        }

        $validChannelIds = $this->fetchValidChannelIds();
        $updateIds = array_intersect($validChannelIds, $channelIds);
        $values = array();
        $yesterday = $date;
        foreach ($updateIds as $ui)
        {
            $row = "('$yesterday', $ui, {$channelToStat[$ui]['browse_ip']}, {$channelToStat[$ui]['browse_pv']},
                {$channelToStat[$ui]['browse_uv']}, {$channelToStat[$ui]['click_ip']},
                {$channelToStat[$ui]['click_pv']}, {$channelToStat[$ui]['click_uv']}, NOW())";
            array_push($values, $row);
        }

        if ($values)
        {
            $valueStr = implode(',', $values);

            $sql = "INSERT cp_50bang_web (date, channel_id, browse_ip, browse_pv, browse_uv, click_ip, click_pv, click_uv,
                created) VALUES $valueStr
                ON DUPLICATE KEY UPDATE browse_ip = VALUES(browse_ip), browse_pv = VALUES(browse_pv),
                browse_uv = VALUES(browse_uv), click_ip = VALUES(click_ip), click_pv = VALUES(click_pv),
                click_uv = VALUES(click_uv)";
            $this->db->query($sql);
        }
    }

    public function deliveryWebAllContent($typeToStat, $date)
    {
        $row = array(
            'browse_ip' => 0,
            'browse_pv' => 0,
            'browse_uv' => 0,
            'click_ip'  => 0,
            'click_pv'  => 0,
            'click_uv'  => 0,
        );
        $fields = array('ip', 'pv', 'uv');
        foreach ($typeToStat as $prefix => $statistics)
        {
            $records = unserialize($statistics);
            foreach ($fields as $fd)
            {
                $row[$prefix . '_' . $fd] = $records[0][$fd];
            }
        }

        $sql = "INSERT cp_50bang_web_all (date, browse_ip, browse_pv, browse_uv, click_ip, click_pv, click_uv, created)
            VALUES ('$date', {$row['browse_ip']}, {$row['browse_pv']}, {$row['browse_uv']}, {$row['click_ip']}, 
            {$row['click_pv']}, {$row['click_uv']}, NOW()) 
            ON DUPLICATE KEY UPDATE browse_ip = VALUES(browse_ip), browse_pv = VALUES(browse_pv), 
            browse_uv = VALUES(browse_uv), click_ip = VALUES(click_ip), click_pv = VALUES(click_pv), 
            click_uv = VALUES(click_uv)";
        $this->db->query($sql);
    }

    private function fetchValidChannelIds()
    {
        $sql = "SELECT id FROM cp_channel";

        return $this->db->query($sql)->getCol();
    }

    private function fetchValidChannels()
    {
        $sql = "SELECT id, name FROM cp_channel";
        $results = $this->db->query($sql)->getAll();
        $channels = array();
        foreach ($results as $rs)
        {
            $channels[$rs['id']] = $rs['name'];
        }

        return $channels;
    }

    public function cleanTable()
    {
        $this->db->query("DELETE FROM cp_50bang_web");
        $this->db->query("ALTER TABLE cp_50bang_web AUTO_INCREMENT = 1");
        $this->db->query("DELETE FROM cp_50bang_app");
        $this->db->query("ALTER TABLE cp_50bang_app AUTO_INCREMENT = 1");
    }
    
}
