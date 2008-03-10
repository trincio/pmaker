<?php
/**
 * class.outputDocument.php
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */
//
// It works with the table OUTPUT_DOCUMENT in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * ProcessOutputDocument - ProcessOutputDocument class
 * @package ProcessMaker
 * @author David S. Callizaya S.
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class OutputDocument extends PmObject
{

	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
  function SetTo( $oConnection = null)
  {
  	if ($oConnection)
		{
			return parent::setTo($oConnection, 'OUTPUT_DOCUMENT', array('OUT_DOC_UID','PRO_UID'));
		}
		else
		{
			return;
		}
	}

  /*
	* Load the user information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{
  		$this->table_keys	= array('OUT_DOC_UID' );
  		parent::load($sUID);
  		$proFields = $this->Fields;

  		/** Start Comment: Charge OUT_DOC_TITLE and OUT_DOC_DESCRIPTION */
  	  $this->content->load(array('CON_CATEGORY' => "OUT_DOC_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['OUT_DOC_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "OUT_DOC_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['OUT_DOC_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "OUT_DOC_FILENAME", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['OUT_DOC_FILENAME'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "OUT_DOC_TEMPLATE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['OUT_DOC_TEMPLATE'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $proFields;
			/** End Comment*/

			$this->table_keys = array('OUT_DOC_UID','PRO_UID');
  	  return ;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the User UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /**
	 * Save the Fields in OUTPUT_DOCUMENT
   *
   *
   * @author David S. Callizaya S.
   * @access public
   * @param  array $fields
   * @return string
   * return uid OUTPUT_DOCUMENT
  **/

  function save ($fields)
  {
		$this->Fields = array(  'PRO_UID'              => (isset($fields['PRO_UID'])              ? $fields['PRO_UID']              : $this->Fields['PRO_UID']));
														//'OUT_DOC_FILENAME' => (isset($fields['OUT_DOC_FILENAME']) ? $fields['OUT_DOC_FILENAME'] : ( isset ( $this->Fields['OUT_DOC_FILENAME'])   ? $this->Fields['OUT_DOC_FILENAME'] : 'outputDocument' )) );

    //if is a new document we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['OUT_DOC_UID'])){
    	$this->Fields['OUT_DOC_UID'] = $fields['OUT_DOC_UID'];
			$fields['CON_ID'] = $fields['OUT_DOC_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['OUT_DOC_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$uid = $this->Fields['OUT_DOC_UID'];

  	parent::save();

		/** Start Comment: Save in the table CONTENT */
  	$this->content->saveContent('OUT_DOC_TITLE',$fields);
		$this->content->saveContent('OUT_DOC_DESCRIPTION',$fields);
		$this->content->saveContent('OUT_DOC_FILENAME',$fields);
		$this->content->saveContent('OUT_DOC_TEMPLATE',$fields);
		/** End Comment */

		 return $uid;

  }

 /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID)
  {
  	if (isset($sUID))
		{
			$this->table_keys	= array('OUT_DOC_UID' );
  	  $this->Fields['OUT_DOC_UID'] = $sUID;
  	  parent::delete();
  	  $this->table_keys = array('OUT_DOC_UID','PRO_UID');
  		$this->content->table_keys	= array('CON_ID' );
  	  $this->content->Fields['CON_ID'] = $sUID;
  	  $this->content->delete();
  	  return ;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Output Document UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Generate the output document
	* @param string $sUID
	* @param array $aFields
	* @param string $sPath
	* @return variant
	*/
	function generate($sUID, $aFields, $sPath, $sFilename)
  {
  	if (($sUID != '') && is_array($aFields) && ($sPath != ''))
  	{
  		if (!is_array($this->Fields))
  		{
  	    $this->load($sUID);
  	  }
  	  $sContent = G::replaceDataField($this->Fields['OUT_DOC_TEMPLATE'], $aFields);
  	  G::verifyPath($sPath, true);
  	  /* Start - Create .doc */
  	  $oFile = fopen($sPath .  $sFilename . '.doc', 'wb');
  	  fwrite($oFile, "MIME-Version: 1.0\n");
      fwrite($oFile, "Content-Type: multipart/related; boundary=\"==boundary\"; type=\"text/html;\"\n\n");
      fwrite($oFile, "--==boundary\nContent-Type: text/html;\n\n");
      fwrite($oFile, "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /></head><body>\n");
      fwrite($oFile, $sContent);
      fwrite($oFile, "\n</body></html>\n\n");
      fwrite($oFile, "--==boundary--\n");
  	  fclose($oFile);
  	  /* End - Create .doc */
  	  /* Start - Create .pdf */
  	  $oFile = fopen($sPath .  $sFilename . '.html', 'wb');
  	  fwrite($oFile, $sContent);
  	  fclose($oFile);
  	  define('PATH_OUTPUT_FILE_DIRECTORY', PATH_HTML . 'files/' . $_SESSION['APPLICATION'] . '/outdocs/');
  	  require_once(PATH_THIRDPARTY . 'html2ps_pdf/config.inc.php');
      require_once(PATH_THIRDPARTY . 'html2ps_pdf/pipeline.factory.class.php');
      parse_config_file(PATH_THIRDPARTY . 'html2ps_pdf/html2ps.config');
      $GLOBALS['g_config'] = array('cssmedia'                => 'screen',
                                   'media'                   => 'Letter',
                                   'scalepoints'             => true,
                                   'renderimages'            => true,
                                   'renderfields'            => true,
                                   'renderforms'             => false,
                                   'pslevel'                 => 3,
                                   'renderlinks'             => true,
                                   'pagewidth'               => 800,
                                   'landscape'               => false,
                                   'method'                  => 'fpdf',
                                   'margins'                 => array('left' => 15, 'right' => 15, 'top' => 15, 'bottom' => 15,),
                                   'encoding'                => '',
                                   'ps2pdf'                  => false,
                                   'compress'                => false,
                                   'output'                  => 2,
                                   'pdfversion'              => '1.3',
                                   'transparency_workaround' => false,
                                   'imagequality_workaround' => false,
                                   'draw_page_border'        => isset($_REQUEST['pageborder']),
                                   'debugbox'                => false,
                                   'html2xhtml'              => true,
                                   'mode'                    => 'html',
                                   'smartpagebreak'          => true);
      $g_media = Media::predefined($GLOBALS['g_config']['media']);
      $g_media->set_landscape($GLOBALS['g_config']['landscape']);
      $g_media->set_margins($GLOBALS['g_config']['margins']);
      $g_media->set_pixels($GLOBALS['g_config']['pagewidth']);
      $pipeline = new Pipeline();
      if (extension_loaded('curl'))
      {
        require_once(HTML2PS_DIR . 'fetcher.url.curl.class.php');
        $pipeline->fetchers = array(new FetcherURLCurl());
        if ($proxy != '')
        {
          $pipeline->fetchers[0]->set_proxy($proxy);
        }
      }
      else
      {
        require_once(HTML2PS_DIR . 'fetcher.url.class.php');
        $pipeline->fetchers[] = new FetcherURL();
      }
      $pipeline->data_filters[] = new DataFilterDoctype();
      $pipeline->data_filters[] = new DataFilterUTF8($GLOBALS['g_config']['encoding']);
      if ($GLOBALS['g_config']['html2xhtml'])
      {
        $pipeline->data_filters[] = new DataFilterHTML2XHTML();
      }
      else
      {
        $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
      }
      $pipeline->parser = new ParserXHTML();
      $pipeline->pre_tree_filters = array();
      $header_html = '';
      $footer_html = '';
      $filter      = new PreTreeFilterHeaderFooter($header_html, $footer_html);
      $pipeline->pre_tree_filters[] = $filter;
      if ($GLOBALS['g_config']['renderfields'])
      {
        $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
      }
      if ($GLOBALS['g_config']['method'] === 'ps')
      {
        $pipeline->layout_engine = new LayoutEnginePS();
      }
      else
      {
        $pipeline->layout_engine = new LayoutEngineDefault();
      }
      $pipeline->post_tree_filters = array();
      if ($GLOBALS['g_config']['pslevel'] == 3)
      {
        $image_encoder = new PSL3ImageEncoderStream();
      }
      else
      {
        $image_encoder = new PSL2ImageEncoderStream();
      }
      switch ($GLOBALS['g_config']['method'])
      {
       case 'fastps':
         if ($GLOBALS['g_config']['pslevel'] == 3)
         {
           $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
         }
         else
         {
           $pipeline->output_driver = new OutputDriverFastPSLevel2($image_encoder);
         }
       break;
       case 'pdflib':
         $pipeline->output_driver = new OutputDriverPDFLIB16($GLOBALS['g_config']['pdfversion']);
       break;
       case 'fpdf':
         $pipeline->output_driver = new OutputDriverFPDF();
       break;
       case 'png':
         $pipeline->output_driver = new OutputDriverPNG();
       break;
       case 'pcl':
         $pipeline->output_driver = new OutputDriverPCL();
       break;
       default:
         die('Unknown output method');
      }
      $watermark_text = $GLOBALS['g_config']['watermarkhtml'];
      $pipeline->output_driver->set_watermark($watermark_text);
      if ($watermark_text != '')
      {
        $dispatcher =& $pipeline->getDispatcher();
      }
      if ($GLOBALS['g_config']['debugbox'])
      {
        $pipeline->output_driver->set_debug_boxes(true);
      }
      if ($GLOBALS['g_config']['draw_page_border'])
      {
        $pipeline->output_driver->set_show_page_border(true);
      }
      if ($GLOBALS['g_config']['ps2pdf'])
      {
        $pipeline->output_filters[] = new OutputFilterPS2PDF($GLOBALS['g_config']['pdfversion']);
      }
      if ($GLOBALS['g_config']['compress'] && $GLOBALS['g_config']['method'] == 'fastps')
      {
        $pipeline->output_filters[] = new OutputFilterGZip();
      }
      if ($GLOBALS['g_config']['process_mode'] == 'batch')
      {
        $filename = 'batch';
      }
      else
      {
        $filename = $sFilename;
      }
      switch ($GLOBALS['g_config']['output'])
      {
       case 0:
         $pipeline->destination = new DestinationBrowser($filename);
         break;
       case 1:
         $pipeline->destination = new DestinationDownload($filename);
         break;
       case 2:
         $pipeline->destination = new DestinationFile($filename);
         break;
      }
      $status = $pipeline->process(($_SERVER['SERVER_PORT'] == '243' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/files/' . $_SESSION['APPLICATION'] . '/outdocs/' . $sFilename . '.html', $g_media);
  	  /* End - Create .pdf */
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a generate method without send the Output Document UID, fields to use and the file path!',
    	                        'G_Error',
    	                        true);
    }
  }
}
?>