<?php



// Read in CSV file


//Read data into data obj
$csvData = file_get_contents($csvFile);

//Set extension
$fileExtension = '.pdf';
//Capture file name
$filename = basename($csvFile, ".csv");

//Set name of PDF to be the name of the CSV
$fullFileName = $filename.$fileExtension;

//Split CSV into countable lines
$file = new SplFileObject($csvFile, 'r');

//Seek to the end of the file
$file->seek(PHP_INT_MAX);

//Print filesize
//TODO this will be used in the future to determine PDF length
echo $file->key() + 1;
$totalRows =  $file->key();



//Determining the number of pages needed based on the length of the data
//50 lines is based on 12pt font
$fileLength = $file->key() /50;
$pages = ceil($fileLength);
$content = 0;
$newrows = str_replace(",", "", $rows);


//splitting up the csv into chunks that will fit on each page
$chunk_size = 50;
$csv_data = array_map('str_getcsv', file($csvFile));
$chunked_data = array_chunk($csv_data, $chunk_size);
//print_r(implode(" ", $chunked_data));
//print_r($chunked_data);
$chunkCount = 1;
$finalChunk = $pages;
$pageCounter = $pages;


// Split CSV data into rows and columns
$rows = explode("\n", $csvData);
$columns = str_getcsv(array_shift($rows));

// Set output file name
$pdfFile = $fullFileName;

// Open new PDF file for writing
$pdf = fopen($pdfFile, "w");

$objNum = 1;   
$versionNum = 0;
$pageNum = $pages;
$space = " ";
$pageSize = " /MediaBox [0 0 612 792]";
$parent = 1;
$resources = 3;
$lineFeed = "\n";
$textPaint = " Tj ";
$return = "T*";
$leading = "14 TL\n";
$contents = 4;
$rowCount = 0;
$o = "obj".$lineFeed;
$eo = "endobj".$lineFeed.$lineFeed;
$font = "<< /Font".$lineFeed." << /F0".$lineFeed."  << /Type /Font".$lineFeed."   /BaseFont /Times-Roman".$lineFeed."    /Subtype /Type1".$lineFeed."  >>".$lineFeed." >>".$lineFeed.">>".$lineFeed.$eo;
$stream = "<< >>".$lineFeed."stream".$lineFeed."BT".$lineFeed." /F0 12 Tf".$lineFeed."40 750 Td".$lineFeed;
$endstream = "ET".$lineFeed."endstream".$lineFeed;
$catalog = 5;
$eof = ">>".$lineFeed."startxref".$lineFeed."%%EOF";
$kids = array();
$kids[] = "[";
$kids[] = "2 0 R";
print_r($kids);
$data = "This is test data";

//$pdfString = stream_get_contents($pdf);

//Header
fwrite($pdf, "");
fwrite($pdf, "%PDF-2.0".$lineFeed."%äãÏÒ".$lineFeed);

//Page Tree (Parent)
fwrite($pdf, $parent.$space.$versionNum.$space.$o);
fwrite($pdf, "<</Type /Pages".$lineFeed);
fwrite($pdf, " /Count ".$pageNum.$lineFeed);
fwrite($pdf, " /Kids [".$objNum+1..$space.$versionNum.$space."R]".$lineFeed.">>".$lineFeed.$eo);

$objNum++;

//Page(s)
fwrite($pdf, $objNum.$space.$versionNum.$space.$o);
fwrite($pdf, "<< /Type /Page".$lineFeed);
fwrite($pdf, $pageSize.$lineFeed);
fwrite($pdf, " /Resources".$space.$resources.$space.$versionNum.$space."R".$lineFeed);
fwrite($pdf, " /Parent".$space.$parent.$space.$versionNum.$space."R".$lineFeed);
fwrite($pdf, " /Contents".$space."[".$contents.$space.$versionNum.$space."R]".$lineFeed.">>".$lineFeed.$eo);

//Resources
fwrite($pdf, $resources.$space.$versionNum.$space.$o);
fwrite($pdf, $font);

//Content
fwrite($pdf, $contents.$space.$versionNum.$space.$o);
fwrite($pdf, $stream);
fwrite($pdf, $leading);

fwrite($pdf, "(".$filename.")".$textPaint.$return.$lineFeed);


if ($rows != null)  {
    //for($x = 100; $x > 0;$x--){
    while ($rowCount <= 50){
fwrite($pdf, "(".$rows[$rowCount].")".$textPaint.$return.$lineFeed);
echo $rowCount." ";
 $rowCount++;
}}




fwrite($pdf, $endstream.$eo);

//Catalog
fwrite($pdf, $catalog.$space.$versionNum.$space.$o);
fwrite($pdf, "<< /Type /Catalog".$lineFeed." /Pages".$space.$parent.$space.$versionNum.$space."R".$lineFeed.">>".$lineFeed);
fwrite($pdf, $eo);

//Cross-Refrence Table
fwrite($pdf, "xref".$lineFeed.$lineFeed);

//Trailer
fwrite($pdf, "trailer".$lineFeed);
fwrite($pdf, "<< /Root ".$catalog.$space.$versionNum.$space."R".$lineFeed);
fwrite($pdf, $eof);

createNewPage($pdf, 8, 5, $data);

function createNewPage(resourse $pdf, int $objectNumber, int $contentsNumber, string $data){
    fwrite($pdf, $objectNumber." 0 obj\n");
    fwrite($pdf, "<</Type/Page/Parent 1 0 R/Resources 3 0 R/MediaBox[0 0 612 792]/Group<</S/Transparency/CS/DeviceRGB/I true>>/Contents ".$contentsNumber." 0 R>>");
    fwrite($pdf, "endobj\n\n");

    fwrite($pdf, $contentsNumber." 0 obj\n<< >>\nstream\n /F0 12 Tf\n40 700 td\n".$data." Tj\nendstrean\nendobj\n\n");

    


}


/*
while($pages > 1){
    $pages--;
    echo $pages;
//loop if there are more than one pages
// Write PDF header
    foreach($chunked_data as $chunk) {      
            if ($finalChunk >= 1){            
            $chunkName = "chunk".$finalChunk;
            $chunkContents = $row[$finalChunk];
            echo "Final Chunk: ".$finalChunk. " ";
            echo "Chunk Name: ".$chunkName. " ";
            echo "Chunk Contents: ".$chunkContents. " ";
        }
        }    
    }
*/

// Output PDF document
//header('Content-Type: application/pdf');
//header("Content-Disposition: attachment; filename=\"$pdfFile\"");
//$pdf->Output("$pdfFile", F);
//readfile($pdfFile);

// Delete PDF file
//unlink($pdfFile);


function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

   //echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
?>
