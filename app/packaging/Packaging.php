<?php

/**
 * Packaging class.
 *
 * Calculates packaging for orders
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
class Packaging{

    private function __construct(){}

    public static function getPackingForOrder($od, $items, $packages, $val = 0)
    {
        $return = array();
        $small_satchels = 0;
        $large_satchels = 0;
        $do_satchels = false;
        //manually entered packages override everything
        if( count($packages) )
        {
            foreach($packages as $p)
            {
                $pval = round($val/count($packages), 2);
                $array['item_reference'] = Utility::generateRandString();
                $array['width'] = $p['width'];
                $array['height'] = $p['height'];
                $array['depth'] = $p['depth'];
                $array['weight'] = $p['weight'];
                $array['type_code'] = 'CTN';
                $array['pieces'] = 1;
                $return[] = $array;
            }
        }
        //client specific packaging
        elseif($od['client_id'] == 5)   //Nuchev
        {
            $total_cans = $total_toys = $total_sachets = $weight = 0;
            foreach($items as $i)
            {
                $array['item_reference'] = $i['item_id'];
                if($i['item_id'] == 5807 || $i['item_id'] == 5808 || $i['item_id'] == 5809 )   //sachets
                {
                    $total_sachets += $i['qty'];
                }
                if($i['hunters_goods_type'] == 7  || $i['pre_packed'] > 0)
                {
                    $w = $i['width'];
                    $h = $i['height'];
                    $d = $i['depth'];
                    $weight += $i['weight'];
                    /*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    SOMETHING NEEDS TO HAPPEN HERE

                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
                }
                elseif($i['item_id'] == 5856 || $i['item_id'] == 6127 || $i['item_id'] == 5873 || $i['item_id'] == 6121 || $i['item_id'] == 5884 || $i['item_id'] == 5872 || $i['item_id'] == 10925)   //oli goat book and wobblers
                {
                    //do nothing
                }
                elseif($i['item_id'] == 5855 || $i['item_id'] == 6014 || $i['item_id'] == 10941)  //ugg boots and goats and jellycats
                {
                    $total_toys += $i['qty'];
                }
                elseif($i['item_id'] == 277 || $i['item_id'] == 5861 || $i['item_id'] == 5891)    ///sippy cups
                {
                    $total_toys += $i['qty'] / 2;
                }
                else    //treat as a can
                {
                    $total_cans += $i['qty'];
                }
            }
            $total_toys = ceil($total_toys);
            if($total_toys > 0 || $total_cans > 0 || $total_sachets > 0)
            {
                if($total_toys > 0)
                {
                    $tboxes = self::getNuchevSoftBoxes($total_toys);
                    foreach($tboxes as $b)
                    {
                        list($w, $d, $h) = $b['dimensions'];
                        $c = 0;
                        while($c < $b['count'])
                        {
                            $array['width'] = $w;
                            $array['height'] = $h;
                            $array['depth'] = $d;
                            $array['weight'] = $b['weight'];
                            $array['pieces'] = 1;
                            $array['type_code'] = 'CTN';
                            $return[] = $array;
                            ++$c;
                        }

                    }
                }
                if($total_cans > 0)
                {
                    $boxes = self::getNuchevBoxes($total_cans);
                    foreach($boxes as $b)
                    {
                        list($w, $d, $h) = $b['dimensions'];
                        $c = 0;
                        while($c < $b['count'])
                        {
                            $array['width'] = $w;
                            $array['height'] = $h;
                            $array['depth'] = $d;
                            $array['weight'] = $b['weight'];
                            $array['pieces'] = 1;
                            $array['type_code'] = 'CTN';
                            $return[] = $array;
                            ++$c;
                        }

                    }
                }
                if($total_sachets > 0 && ($total_cans == 0 && $total_toys == 0))
                {
                    if($total_sachets % 150 === 0)
                    {
                        $sachet_box = 1;
                        while($sachet_box <= $total_sachets / 150)
                        {
                            $array['width'] = 42;
                            $array['height'] = 19;
                            $array['depth'] = 28;
                            $array['weight'] = 4.3;
                            $array['pieces'] = 1;
                            $array['type_code'] = 'CTN';
                            $return[] = $array;
                            ++$sachet_box;
                        }
                        $total_sachets = $total_sachets - ( ($sachet_box - 1) * 150);
                    }
                    elseif($total_sachets > 150)
                    {
                        $sachet_box = 1;
                        while($sachet_box <= $total_sachets / 150)
                        {
                            $array['width'] = 42;
                            $array['height'] = 19;
                            $array['depth'] = 28;
                            $array['weight'] = 4.3;
                            $array['pieces'] = 1;
                            $array['type_code'] = 'CTN';
                            $return[] = $array;
                            ++$sachet_box;
                        }
                        $total_sachets = $total_sachets - ( ($sachet_box - 1) * 150);
                    }
                    if($total_sachets > 50)
                    {
                        $c = 1;
                        while($c <= ceil($total_sachets / 50))
                        {
                            $array['width'] = 24;
                            $array['height'] = 2;
                            $array['depth'] = 30;
                            $array['weight'] = 2.5 + 0.033;
                            $array['pieces'] = 1;
                            $array['type_code'] = 'CTN';
                            $return[] = $array;
                            ++$c;
                        }
                    }
                    elseif($total_sachets > 30)
                    {
                        $c = 1;
                        while($c <= ceil($total_sachets / 30))
                        {
                            $array['width'] = 21;
                            $array['height'] = 2;
                            $array['depth'] = 25;
                            $array['weight'] = 1.5 + 0.025;
                            $array['pieces'] = 1;
                            $array['type_code'] = 'CTN';
                            $return[] = $array;
                            ++$c;
                        }
                    }
                    elseif($total_sachets > 0)
                    {
                        $array['width'] = 21;
                        $array['height'] = 2;
                        $array['depth'] = 25;
                        $array['weight'] = $total_sachets * 0.05 + 0.025;
                        $array['pieces'] = 1;
                        $array['type_code'] = 'CTN';
                        $return[] = $array;
                    }
                }
            }
            else
            {
                $array['width'] = $w;
                $array['height'] = $h;
                $array['depth'] = $d;
                $array['weight'] = $weight;
                $array['pieces'] = 1;
                $array['type_code'] = 'CTN';
                $return[] = $array;
            }
        }
        elseif($od['client_id'] == 6)   //Big Bottle
        {
            $total_bottles = 0;
            $ar_bottles = 0;
            $weight = 0;
            foreach($items as $i)
            {
                if( in_array($i['id'], Config::get('BB_ADRANGE_IDS') ) )
                {
                    $weight += $i['weight'];
                    $ar_bottles += $i['qty'];
                }
                else
                {
                    $total_bottles += $i['qty'];
                }
                $array['item_reference'] = $i['item_id'];
            }
            list($w, $d, $h) = Config::get('BBBOX_DIMENSIONS')[$total_bottles];
            $array['width'] = $w;
            $array['height'] = $h;
            $array['depth'] = $d;
            $array['weight'] = Config::get('BBBOX_WEIGHTS')[$total_bottles] + $weight;
            $array['pieces'] = 1;
            $array['type_code'] = 'CTN';
            $return[] = $array;
        }
        //item specific packages
        else
        {
            $weight = 0;
            foreach($items as $i)
            {
                if($i['hunters_goods_type'] == 20)
                {
                    $do_satchels = true;
                    $description = (empty($i['description']))? $i['name']: $i['description'];
                    $description = mb_strimwidth( $description , 0 , 40 ); //auspost will not allow this to be more than 40 characters
                    $val = ($i['price'] == 0)? 1.00 : $i['price'];
                    $weight += $i['weight'] * $i['qty'];
                    if( !empty($i['satchel_large']) )  $large_satchels += $i['satchel_large'];
                    if( !empty($i['satchel_small']) )  $small_satchels += $i['satchel_small'];
                    continue;
                }
                else
                {
                    $array['width'] = $i['width'];
                    $array['height'] = $i['height'];
                    $array['depth'] = $i['depth'];
                    $array['weight'] = $i['weight'];
                    $array['item_reference'] = $i['item_id'];
                    $array['type_code'] = 'CTN';
                    $array['pieces'] = $i['qty'];
                    $return[] = $array;
                }
            }
            if($do_satchels)
            {
                $array['item_reference'] = $items[0]['item_id'];
                $whole_small = ceil($small_satchels);

                if($whole_small > 1)
                {
                    $large_satchels += floor($whole_small / 2);
                    $whole_small = $whole_small % 2;
                }
                $whole_large = ceil($large_satchels);
                $large_space = round($whole_large - $large_satchels, 1, PHP_ROUND_HALF_DOWN);
                if($large_space >= 0.5)
                {
                    --$whole_small;
                    $small_stachels = $whole_small > 0;
                }
                if($large_satchels)
                {
                    $array['width'] = 43;
                    $array['height'] = 32;
                    $array['depth'] = 14;
                    $array['weight'] = $weight;
                    $array['type_code'] = "PPS";
                    $array['pieces'] = $whole_large;
                    $return[] = $array;
                }
                if($small_satchels)
                {
                    $array['width'] = 23;
                    $array['height'] = 34;
                    $array['depth'] = 8;
                    $array['weight'] = $weight;
                    $array['type_code'] = "PPS";
                    $array['pieces'] = $whole_small;
                    $return[] = $array;
                }

            }
        }

        return $return;
    }

    private static function getNuchevSoftBoxes($qty)
    {
        $six = floor( $qty / 6 );
        $four = floor( ($qty - $six * 6)/4 );
        $three  = floor( ($qty - $six * 6 - $four * 4)/3 );
        $two = floor( ($qty - $six * 6 - $four * 4 - $three * 3)/2 );
        $one = floor( ($qty - $six * 6 - $four * 4 - $three * 3 - $two * 2)/1 );

        $return = array();
        if($six > 0)
        {
            $return[] = array(
                'count'         =>  $six,
                'weight'        =>  Config::get('nuchev6softbox')['weight'],
                'dimensions'    =>  Config::get('nuchev6softbox')['dimensions']
            );
        }
        if($four > 0)
        {
            $return[] = array(
                'count'         =>  $four,
                'weight'        =>  Config::get('nuchev4softbox')['weight'],
                'dimensions'    =>  Config::get('nuchev4softbox')['dimensions']
            );
        }
        if($three > 0)
        {
            $return[] = array(
                'count'         =>  $three,
                'weight'        =>  Config::get('nuchev3softbox')['weight'],
                'dimensions'    =>  Config::get('nuchev3softbox')['dimensions']
            );
        }
        if($two > 0)
        {
            $return[] = array(
                'count'         =>  $two,
                'weight'        =>  Config::get('nuchev2softbox')['weight'],
                'dimensions'    =>  Config::get('nuchev2softbox')['dimensions']
            );
        }
        if($one > 0)
        {
            $return[] = array(
                'count'         =>  $one,
                'weight'        =>  Config::get('nuchev1softbox')['weight'],
                'dimensions'    =>  Config::get('nuchev1softbox')['dimensions']
            );
        }

        return $return;
    }

    private static function getNuchevBoxes($qty)
    {
        $six    = floor( $qty / 6 );
        $five   = floor( ($qty - $six * 6)/5 );
        $four   = floor( ($qty - $six * 6 - $five * 5)/4 );
        $three  = floor( ($qty - $six * 6 - $five * 5 - $four * 4)/3 );
        $two    = floor( ($qty - $six * 6 - $five * 5 - $four * 4 - $three * 3)/2 );
        $one    = floor( ($qty - $six * 6 - $five * 5 - $four * 4 - $three * 3 - $two * 2)/1 );

        $return = array();
        if($six > 0)
        {
            $return[] = array(
                'count'         =>  $six,
                'weight'        =>  Config::get('nuchev6box')['weight'],
                'dimensions'    =>  Config::get('nuchev6box')['dimensions']
            );
        }
        if($five > 0)
        {
            $return[] = array(
                'count'         =>  $five,
                'weight'        =>  Config::get('nuchev6box')['weight'],
                'dimensions'    =>  Config::get('nuchev6box')['dimensions']
            );
        }
        if($four > 0)
        {
            $return[] = array(
                'count'         =>  $four,
                'weight'        =>  Config::get('nuchev4box')['weight'],
                'dimensions'    =>  Config::get('nuchev4box')['dimensions']
            );
        }
        if($three > 0)
        {
            $return[] = array(
                'count'         =>  $three,
                'weight'        =>  Config::get('nuchev3box')['weight'],
                'dimensions'    =>  Config::get('nuchev3box')['dimensions']
            );
        }
        if($two > 0)
        {
            $return[] = array(
                'count'         =>  $two,
                'weight'        =>  Config::get('nuchev2box')['weight'],
                'dimensions'    =>  Config::get('nuchev2box')['dimensions']
            );
        }
        if($one > 0)
        {
            $return[] = array(
                'count'         =>  $one,
                'weight'        =>  Config::get('nuchev1box')['weight'],
                'dimensions'    =>  Config::get('nuchev1box')['dimensions']
            );
        }

        return $return;
    }

}
?>