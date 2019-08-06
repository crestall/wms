<?php

/**
 * The addresslabels class.
 *
 * handles the printing of address labels
 * pdf public functionality to come
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */
 class AddressLabels{

    var $format				= "word"; // word, pdf or html
    var $labels_across		= 2; // number of labels horizontally across the page
    var $labels_down		= 7; // number of labels vertically down the page
    var $label_height		= 3.81; // the height, in centimeters, of each label
    var $label_width		= 9.9; // the width, in centimeters, of each label
    var $pitch_horizontal	= 10.16; // the width of the label plus horizontal spacing, in centimeters, of each label
    var $pitch_vertical		= 3.81; // the width of the label plus vertical spacing, in centimeters, of each label
    var $page_margin_top	= 1.51; // the top page margin, in centimeters
    var $page_margin_side	= 0.47; // the left/right page margin, in centimeters
    var $page_height 		= 29.69; // the height of the paper. defaults to A4 dimensions
    var $page_width 		= 21; // the width of the paper. defaults to A4 dimensions
    var $align_horizontal 	= "left"; // the horizontal justification of each label. left, center or right
    var $align_vertical 	= "center"; // the vertical alignment of each label. top, center or bottom
    var $padding_left		= 1; // the left padding, in centimeters, of each label
    var $padding_top		= 0; // the top padding, in centimeters, of each label
    var $font_face			= "Arial"; // The font face to use
    var $font_size			= 12; // The font size, in pt, to use

    var $addresses 			= array();
    var $layout 			= "";

    public function __construct($config = array())
    {
        if (count($config) > 0)
        {
            $this->initialize($config);
        }
    }

    public function initialize($config = array())
    {
        foreach ($config as $key => $val)
        {
            if (isset($this->$key))
            {
                $this->$key = $val;
            }
        }
    }

    public function output($addresses=array())
    {

        $this->addresses = $addresses;
        $this->labels_total = $this->labels_across*$this->labels_down;

        if (count($this->addresses)) {
            switch ($this->format) {
                case "word": { $output = $this->generate_labels_word();  break; }
                case "html": { $output = $this->generate_labels_html();  break; }
                case "pdf": { die("PDF output is work in progress. Check back soon :)"); $output = $this->generate_labels_pdf();  break; }
                default: { die("Invalid format provided. Must be word, pdf or html"); }
            }
        }else{
            die("No addresses provided");
        }

    }

    public function generate_labels_pdf()
    {

        // calculate the padding
        $this->padding_left = $this->convert($this->padding_left, "cm", "px");
        $this->padding_top = $this->convert($this->padding_top, "cm", "pt");

        // calculate left and top margins
        $this->page_margin_side = $this->convert($this->page_margin_side, "cm", "cm");
        $this->page_margin_top = $this->convert($this->page_margin_top, "cm", "cm");

        // calculate label width and label height
        $this->label_height = $this->convert($this->label_height, "cm", "pt");
        $this->label_width = $this->convert($this->label_width, "cm", "pt");

        // calculate paper width and height
        $this->page_height = $this->convert($this->page_height, "cm", "cm");
        $this->page_width = $this->convert($this->page_width, "cm", "cm");

        // calculate the spacing
        $this->pitch_horizontal = $this->convert($this->pitch_horizontal, "cm", "pt");
        $this->pitch_vertical = $this->convert($this->pitch_vertical, "cm", "pt");

        $CI =& get_instance();
        $CI->load->library("cezpdf", array($this->page_width, $this->page_height));

        $CI->cezpdf->selectFont(APPPATH.'libraries/fonts/Helvetica.afm');
        $CI->cezpdf->ezSetCmMargins($this->page_margin_top, 0, $this->page_margin_side, $this->page_margin_side);

        // setup columns
        $col_names = array();
        $col_options = array();
        for ($i=0; $i<$this->labels_across; $i++) {
            if ($this->pitch_horizontal-$this->label_width>0 && $i>0) {
                $col_names['padding'.$i] = '';
                $col_options['padding'.$i] = array('width'=>$this->pitch_horizontal-$this->label_width);
            }
            $col_names['column'.$i] = '';
            $col_options['column'.$i] = array('width'=>$this->label_width);
        }
        $table_options = array('width'=>550, 'showLines'=>0, 'showHeadings'=>0, 'shaded'=>0, 'cols'=>$col_options);

        $num_x = 0;
        $num_y = 0;
        $num_total = 0;
        $table_data = array();
        $row_table_data = array();
        foreach ($this->addresses as $address) {

            if ($num_total==$this->labels_total) {
                array_push($table_data, $row_table_data);
                $CI->cezpdf->ezTable($table_data, $col_names, '', $table_options);
                $CI->cezpdf->ezNewPage();
                $num_x = 0;
                $num_y = 0;
                $table_data = array();
                $row_table_data = array();
            }

            if ($num_x==$this->labels_across) {
                array_push($table_data, $row_table_data);
                $row_table_data = array();
                $num_y++;
                $num_x = 0;
            }

            if ($num_x<$this->labels_across) {

                // loop through and replace address elements
                $prespace = "";
                for ($i=0; $i<$this->padding_left/4; $i++) {
                    $prespace .= " ";
                }
                $search_array = array("<br />", "<br>", "<BR />", "<BR>");
                $replace_array = array("\n".$prespace, "\n".$prespace, "\n".$prespace, "\n".$prespace);
                foreach ($address as $address_key=>$address_value) {
                    array_push($search_array, $address_key);
                    array_push($replace_array, $address[$address_key]);
                }
                if ($this->pitch_horizontal-$this->label_width>0 && $num_x>0) {
                    $row_table_data['padding'.$num_x] = '';
                }
                $row_table_data['column'.$num_x] = $prespace.str_replace($search_array, $replace_array, $this->layout);

            }

            $num_x++;
            $num_total++;

        }
        array_push($table_data, $row_table_data);
        $CI->cezpdf->ezTable($table_data, $col_names, '', $table_options);
        $CI->cezpdf->ezStream();

    }

    public function generate_labels_html()
    {

        // calculate the padding
        $this->padding_left = $this->convert($this->padding_left, "cm", "cm");
        $this->padding_top = $this->convert($this->padding_top, "cm", "cm");

        // calculate left and top margins
        $this->page_margin_side = $this->convert($this->page_margin_side, "cm", "cm");
        $this->page_margin_top = $this->convert($this->page_margin_top, "cm", "cm");

        // calculate label width and label height
        $this->label_height = $this->convert($this->label_height, "cm", "cm");
        $this->label_width = $this->convert($this->label_width, "cm", "cm");

        // calculate paper width and height
        $this->page_height = $this->convert($this->page_height, "cm", "cm");
        $this->page_width = $this->convert($this->page_width, "cm", "cm");

        // calculate the spacing
        $this->pitch_horizontal = $this->convert($this->pitch_horizontal, "cm", "cm");
        $this->pitch_vertical = $this->convert($this->pitch_vertical, "cm", "cm");

        $output = '<html>
            <head>

            </head>
            <body style="margin:0; font-family:'.$this->font_face.'; font-size:'.$this->font_size.'pt">';

        // loop through addresses
        $num_x = 0;
        $num_y = 0;
        $num_total = 0;
        foreach ($this->addresses as $address) {

            // add page break / start and end table tag
            if ($num_total==$this->labels_total) {
                $output .= '</tr></table><br style="page-break-after:always" />';
                $num_total = 0;
                $num_x = 0;
                $num_y = 0;
            }
            if ($num_total==0) { $output .= '<table width="100%" cellpadding="0" cellspacing="0">'; }

            // start and end row tag
            if ($num_x==$this->labels_across) { $output .= '</tr>'; $num_x = 0; $num_y++; }
            if ($num_x==0) {
                if ($this->pitch_vertical-$this->label_height>0 && $num_y>0) { // if row required
                    $output .= '<tr>
                                    <td colspan="'.$this->labels_across.'" style="font-size:1px; height:'.($this->pitch_vertical-$this->label_height).'cm">&nbsp;</td>
                                </tr>';
                }
                $output .= '<tr>';
            }

            if ($this->pitch_horizontal-$this->label_width>0 && $num_x>0) { // if cell required
                $output .= '<td style="width:'.($this->pitch_horizontal-$this->label_width).'cm; font-size:1pt">&nbsp;</td>';
            }

            $output .= '<td style="width:'.$this->label_width.'cm; height:'.$this->label_height.'cm; padding-left:'.$this->padding_left.'cm; padding-top:'.$this->padding_top.'cm" align="'.$this->align_horizontal.'" valign="'.$this->align_vertical.'">';

            // loop through and replace address elements
            $search_array = array();
            $replace_array = array();
            foreach ($address as $address_key=>$address_value) {
                array_push($search_array, $address_key);
                array_push($replace_array, $address[$address_key]);
            }
            $address_item = str_replace($search_array, $replace_array, $this->layout);

            $output .= $address_item;

            $output .= '</td>';

            $num_x++;
            $num_total++;
        }
        // Output any remaining cells from the last row
        for ($i=0; $i<$this->labels_across-$num_x; $i++) {
            if ($this->pitch_horizontal-$this->label_width>0) { // if cell required
                $output .= '<td style="font-size:1pt">&nbsp;</td>';
            }
            $output .= '<td></td>';
        }
        $output .= '</tr>
                </table>

            </body>
        </html>';

        echo $output;

    }

    public function generate_labels_word()
    {

        // calculate the padding
        $this->padding_left = $this->convert($this->padding_left);
        $this->padding_top = $this->convert($this->padding_top);

        // calculate left and top margins
        $this->page_margin_side = $this->convert($this->page_margin_side);
        $this->page_margin_top = $this->convert($this->page_margin_top);

        // calculate label width and label height
        $this->label_height = $this->convert($this->label_height);
        $this->label_width = $this->convert($this->label_width);

        // calculate paper width and height
        $this->page_height = $this->convert($this->page_height);
        $this->page_width = $this->convert($this->page_width);

        // calculate the spacing
        $this->pitch_horizontal = $this->convert($this->pitch_horizontal);
        $this->pitch_vertical = $this->convert($this->pitch_vertical);

        $output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <?mso-application progid="Word.Document"?>
        <w:wordDocument xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml">
            <w:docPr>
                <w:view w:val="print"/>
    				<w:zoom w:val="full-page" w:percent="100"/>
            </w:docPr>
            <w:body>';

        $table_definition = '	<w:tblPr>
                                  	<w:tblW w:w="0" w:type="auto"/>
                                </w:tblPr>
                                <w:tblGrid>
                                   <w:gridCol w:w="'.$this->label_width.'"/>
                                   <w:gridCol w:w="'.$this->label_width.'"/>
                                </w:tblGrid>';

        // loop through addresses
        $num_x = 0;
        $num_y = 0;
        $num_total = 0;
        foreach ($this->addresses as $address) {

            // add page break / start and end table tag
            if ($num_total==$this->labels_total) {
                $output .= '</w:tr></w:tbl><w:p><w:r><w:pageBreakBefore/></w:r></w:p>';
                $num_total = 0;
                $num_x = 0;
                $num_y = 0;
            }
            if ($num_total==0) { $output .= '<w:tbl>'.$table_definition; }

            // start and end row tag
            if ($num_x==$this->labels_across) { $output .= '</w:tr>'; $num_x = 0; $num_y++; }
            if ($num_x==0) {
                if ($this->pitch_vertical-$this->label_height>0 && $num_y>0) { // if row required
                    $output .= '<w:tr><w:trPr><w:trHeight w:val="'.($this->pitch_vertical-$this->label_height).'"/></w:trPr>
                                    <w:tc><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>
                                    <w:tc><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>
                                    <w:tc><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>
                                </w:tr>';
                }
                $output .= '<w:tr><w:trPr><w:trHeight w:val="'.$this->label_height.'"/></w:trPr>';
            }

            if ($this->pitch_horizontal-$this->label_width>0 && $num_x>0) { // if cell required
                $output .= '<w:tc><w:tcPr><w:tcW w:w="'.($this->pitch_horizontal-$this->label_width).'" w:type="dxa"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>';
            }

            $output .= '<w:tc>
                            <w:tcPr>
                                <w:tcW w:w="'.$this->label_width.'" w:type="dxa"/>
                                <w:vAlign w:val="'.$this->align_vertical.'"/>
                            </w:tcPr>
                            <w:p>
                                <w:pPr>
                                    <w:jc w:val="'.$this->align_horizontal.'"/>
                                    <w:spacing w:before="'.$this->padding_top.'"/>
                                    <w:ind w:left="'.$this->padding_left.'"/>
                              	</w:pPr>
                                <w:r>
                                    <w:rPr>
                                        <w:rFonts w:ascii="'.$this->font_face.'" w:h-ansi="'.$this->font_face.'" w:cs="'.$this->font_face.'"/>
                                   		<w:sz w:val="'.($this->font_size*2).'"/>
                                        <w:sz-cs w:val="'.($this->font_size*2).'"/>
                                    </w:rPr>
                                    <w:t>';

            // loop through and replace address elements
            $search_array = array();
            $replace_array = array();
            foreach ($address as $address_key=>$address_value) {
                array_push($search_array, $address_key);
                array_push($replace_array, $address[$address_key]);
            }
            $address_item = str_replace($search_array, $replace_array, $this->layout);

            // replace html with WordML valid tags
            $address_item = str_replace(array("<br />", "<br>", "<BR />", "<BR>"), "<w:br/>", $address_item);

            $output .= $address_item;

            $output .= '</w:t>
                                </w:r>
                            </w:p>
                        </w:tc>';

            $num_x++;
            $num_total++;
        }
        // Output any remaining cells from the last row
        for ($i=0; $i<$this->labels_across-$num_x; $i++) {
            if ($this->pitch_horizontal-$this->label_width>0) { // if cell required
                $output .= '<w:tc><w:tcPr><w:tcW w:w="'.($this->pitch_horizontal-$this->label_width).'" w:type="dxa"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>';
            }
            $output .= '<w:tc><w:tcPr><w:tcW w:w="'.$this->label_width.'" w:type="dxa"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>';
        }
        $output .= '</w:tr>
                </w:tbl>
                <w:sectPr>
                    <w:pgSz w:w="'.$this->page_width.'" w:h="'.$this->page_height.'"/>
                    <w:pgMar w:top="'.$this->page_margin_top.'" w:right="'.$this->page_margin_side.'" w:bottom="0" w:left="'.$this->page_margin_side.'" />
                </w:sectPr>
            </w:body>
        </w:wordDocument>';

        $this->output_file('labels.docx', $output);

    }

    public function convert($input=0, $unit_from="cm", $unit_to="dxa")
    {
        $output = 0;

        switch ($unit_from) {
            case "in": {
                switch ($unit_to) {
                    case "dxa": { $output = ceil($input*1440); break; }
                    case "px": { $output = ceil(($input*2.54)*37.795275591); break; }
                    case "pt": { $output = ceil(($input*2.54)*28.346456693); break; }
                }
                break;
            }
            case "mm": {
                switch ($unit_to) {
                    case "dxa": { $output = ceil((($input/10)/2.54)*1440); break; }
                    case "px": { $output = ceil(($input/10)*37.795275591); break; }
                    case "pt": { $output = ceil(($input/10)*28.346456693); break; }
                }
                break;
            }
            default: { // presume cm
                switch ($unit_to) {
                    case "dxa": { $output = ceil(($input/2.54)*1440); break; }
                    case "px": { $output = ceil($input*37.795275591); break; }
                    case "pt": { $output = ceil($input*28.346456693); break; }
                    case "cm": { $output = $input; break; }
                }
                break;
            }
        }

        return $output;
    }

    public function output_file($filename='', $output='')
    {

        // Set headers
        header("Cache-Control: public");
        header("Content-Type: application/vnd.ms-word.main+xml");
        header("Content-Description: File Transfer");
        header("Content-Disposition: inline; filename=".$filename);
        header("Content-Transfer-Encoding: binary");
        echo $output;

    }

 }

 ?>