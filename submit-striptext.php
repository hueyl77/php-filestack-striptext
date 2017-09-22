<?php
$file_url = isset($_REQUEST['file_url']) ? $_REQUEST['file_url'] : null;

if (!$file_url) {
  die('Missing param: file_url');
}

$temp_filepath = __DIR__ . "/test-docs/tempfile.doc";
file_put_contents($temp_filepath, fopen($file_url, 'r'));

function docx2text($filename) {
   return readZippedXML($filename, "word/document.xml");
 }

function readZippedXML($archiveFile, $dataFile) {
  // Create new ZIP archive
  $zip = new ZipArchive;

  // Open received archive file
  if (true === $zip->open($archiveFile)) {
      // If done, search for the data file in the archive
      if (($index = $zip->locateName($dataFile)) !== false) {
          // If found, read it to the string
          $data = $zip->getFromIndex($index);
          // Close archive file
          $zip->close();
          // Load XML from a string
          // Skip errors and warnings
          $xml = new DOMDocument();
      $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
          // Return data without XML formatting tags
          return strip_tags($xml->saveXML());
      }
      $zip->close();
  }

  // In case of failure return empty string
  return "";
}

echo "\nstripping text ...\n";
$stripped_text = docx2text("test-docs/test-doc.docx");

echo $stripped_text; // Save this contents to DB
unlink ($temp_filepath);
