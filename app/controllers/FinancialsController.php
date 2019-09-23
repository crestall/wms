<?php

/**
 * Financials controller
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class financialsController extends Controller
{
    public function beforeAction()
    {
        parent::beforeAction();
        $action = $this->request->param('action');
        $actions = [
            'procHuntersCheck'
        ];
        $actions = [
            'procHuntersCheck'
        ];
        $this->Security->config("validateForm", false);
        /*  */
        $this->Security->requirePost($actions); 
        if(in_array($action, $actions))
        {
            $this->Security->config("form", [ 'fields' => ['csrf_token']]);
        }

    }

    public function huntersCheck()
    {

        //render the page
        Config::setJsConfig('curPage', "hunters-check");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/huntersCheck.php',
            [
                'page_title'    =>  "Hunters Invoice Checking",
                'show_table'    =>  false
            ]
        );
    }

    public function directfreightCheck()
    {

        //render the page
        Config::setJsConfig('curPage', "hunters-check");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/directfreightCheck.php',
            [
                'page_title'    =>  "Direct Frieght Invoice Checking",
                'show_table'    =>  false
            ]
        );
    }

    public function procHuntersUpdate()
    {
        $this->procHuntersCheck();
    }

    public function procHuntersCheck()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            return $this->redirector->to(PUBLIC_ROOT."financials/hunters-check");
        }
        else
        {
            $skip_first = isset($header_row);
            Config::setJsConfig('curPage', "hunters-check");
            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/huntersCheck.php',
                [
                    'page_title'    => "Hunters Invoice Checking",
                    'show_table'    => true,
                    'csv_array'     => $csv_array,
                    'skip_first'    => $skip_first
                ]
            );
        }
    }

    public function procdfCheck()
    {
        //echo "<pre>",print_r($this->request->data),"</pre>"; die();
        $post_data = array();
        foreach($this->request->data as $field => $value)
        {
            if(!is_array($value))
            {
                ${$field} = $value;
                $post_data[$field] = $value;
            }
        }
        if($_FILES['csv_file']["size"] > 0)
        {
            if ($_FILES['csv_file']['error']  === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES['csv_file']['tmp_name'];
                $csv_array = array_map('str_getcsv', file($tmp_name));
                //echo "<pre>",print_r($csv_array),"</pre>"; die();
            }
            else
            {
            	$error_message = $this->file_upload_error_message($_FILES[$field]['error']);
                Form::setError('csv_file', $error_message);
            }
        }
        else
        {
            Form::setError('csv_file', 'please select a file to upload');
        }
        if(Form::$num_errors > 0)		/* Errors exist, have user correct them */
        {
            Session::set('value_array', $_POST);
            Session::set('error_array', Form::getErrorArray());
            return $this->redirector->to(PUBLIC_ROOT."financials/directfreight-check");
        }
        else
        {
            $skip_first = isset($header_row);
            Config::setJsConfig('curPage', "df-check");
            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/financials/", Config::get('VIEWS_PATH') . 'financials/directfreightCheck.php',
                [
                    'page_title'    => "Hunters Invoice Checking",
                    'show_table'    => true,
                    'csv_array'     => $csv_array,
                    'skip_first'    => $skip_first
                ]
            );
        }
    }

    public function isAuthorized(){
        //$role = Session::getUserRole();
        $role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
        if( isset($role) && ($role === "admin") )
        {
            return true;
        }
        return false;
    }
}
?>