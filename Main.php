<?php

// Read in CSV file
$csvFile = 'C:\Users\Andrew\Documents\GitHub\phpCsvToPdfNoLibraries\Book1.csv';

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

//Determine PDF length based on filesize
//Anything above 50 will create more than one page
$totalRows =  $file->key();

//Determining the number of pages needed based on the length of the data
//50 lines is based on 12pt font
$fileLength = $file->key() /50;
$pages = ceil($fileLength);
$content = 0;

//splitting up the csv into chunks that will fit on each page
$chunk_size = 50;
$csv_data = array_map('str_getcsv', file($csvFile));
$chunked_data = array_chunk($csv_data, $chunk_size);
$chunkCount = 1;
$finalChunk = $pages;
$pageCounter = $pages;

// Split CSV data into rows and columns
$rows = explode("\n", $csvData);
$columns = str_getcsv(array_shift($rows));

//Clean it up a bit
$rows = str_replace("  ", " ", $rows);
$rows = str_replace("   ", " ", $rows);
$rows = str_replace("    ", " ", $rows);
$rows = str_replace("     ", " ", $rows);
$rows = str_replace("      ", " ", $rows);
$rows = str_replace("       ", " ", $rows);
$rows = str_replace("        ", " ", $rows);
$rows = str_replace("   ", " ", $rows);
$rows = str_replace(",", " | ", $rows);
$rows = str_replace("\"", "", $rows);
$rows = str_replace("| 	 | 	 | 	 |  |  |  |  |  |", "", $rows);
$rows = str_replace(" |  |  |  |  |  |  |  |  | ", "", $rows);
$rows = str_replace(" |  |  | ", "|", $rows);
$rows = str_replace("out) ", "out)\n\n", $rows);

// Set output file name
$pdfFile = $fullFileName;

// Open new PDF file for writing
$pdf = fopen($pdfFile, "w");

//Setting variables that will be used to build the PDF elements
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
$kids[] = "/Kids [2 0 R";

//Header
fwrite($pdf, "");
fwrite($pdf, "%PDF-2.0".$lineFeed."%äãÏÒ".$lineFeed);

//Page Tree (Parent)
fwrite($pdf, $parent.$space.$versionNum.$space.$o);
fwrite($pdf, "<</Type /Pages".$lineFeed);
fwrite($pdf, " /Count ".$pageNum.$lineFeed);
fwrite($pdf, " /Kids [".$objNum+1..$space.">>".$lineFeed.$eo);

//Object 2 aka Page 1
fwrite($pdf, "2 0 obj\n<< /Type /Page\n/MediaBox [0 0 612 792]\n/Resources 3 0 R\n/Parent 1 0 R\n/Contents [4 0 R]\n>>\nendobj\n\n");

//Moving to the next object
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

//Setting the filename
fwrite($pdf, "(".$filename.")".$textPaint.$return.$lineFeed);  

//Paginating the data on page 1
if ($rows != null)  {
    while ($rowCount <= 50){
fwrite($pdf, "(".$rows[$rowCount].")".$textPaint.$return.$lineFeed);
 $rowCount++;
}}

//Ending the object
fwrite($pdf, $endstream.$eo);

//Setting the variables for pages after page 1
$count = 0;
$objNumber = 7;
$contNumber = 8;
$lengthNum = 9;
$refNum1 = 51;
$refNum2 = 101;

//Creating hte pages
while ($pages > 1){
    
     //Page Setup
     fwrite($pdf, $objNumber." 0 obj\n");
     fwrite($pdf, "<< /Type /Page\n/MediaBox [0 0 612 792]\n/Resources 3 0 R\n/Parent 1 0 R\n/Contents [".$contNumber." 0 R]\n>>\n");
     fwrite($pdf, "endobj\n\n");
     $kids[] = $objNumber." 0 R ";
     
    //Contents
    fwrite($pdf, $contNumber." 0 obj\n<</Length ".$lengthNum.">>\nstream\nBT\n /F0 12 Tf\n40 750 Td\n14 TL\n");

    //Paginating the data onto the pages
    while ($rowCount <= $refNum2 && $rowCount <= $totalRows){
               
        fwrite($pdf, "(".$rows[$rowCount].")".$textPaint.$return.$lineFeed);
        echo $rowCount." ";
        $rowCount++;       

    }    
    
    //Moving onto addional objects
    $objNumber = $objNumber + 3;
    $contNumber = $contNumber + 3;
    $lengthNum = $lengthNum + 3;
    $pages--;
    $refNum2 = $refNum2 + 50;
    
    //Ending the stream (text)
    fwrite($pdf, ") Tj T*\nET\nendstrean\nendobj\n\n");

    //Length
    fwrite($pdf, $lengthNum." 0 obj\n36\nendobj\n\n");

    
}

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

//Inserting additional objects (visible pages) to Kids to be displayed
$kids[] = "]\n";
$impKids = implode(" ", $kids);
$exploded = fgets($pdf);
$explode = explode(",",$exploded);   
$insertPosition = fseek($pdf, 40);
$modifiedContent = substr_replace($explode, $impKids, $insertPosition);
$finalModified = implode(" ", $modifiedContent);
fwrite($pdf, $finalModified);

//Function to create new pages for data that extends beyond page 1
//Accepts args for Object Number, Contents Number, Length Number, the Data to be paginated, and the object to be inserted into Kids
//The Object, Contents, and Length number will ALWAYS be sequential, E.g. 7, 8, 9; 14, 15, 16. 
//The Object number will ALWAYS be the same as the number being inserted into Kids.
function createNewPage( $pdf, int $objectNumber, int $contentsNumber, int $lengthNumber, string $data, $kids){
    //Page Setup
    fwrite($pdf, $objectNumber." 0 obj\n");
    fwrite($pdf, "<< /Type /Page\n/MediaBox [0 0 612 792]\n/Resources 3 0 R\n/Parent 1 0 R\n/Contents [".$contentsNumber." 0 R]\n>>\n");
    fwrite($pdf, "endobj\n\n");
    $kids[] = $objectNumber." 0 R ";    

    //Contents
    fwrite($pdf, $contentsNumber." 0 obj\n<</Length ".$lengthNumber.">>\nstream\nBT\n /F0 12 Tf\n40 750 Td\n14 TL\n(".$data.") Tj T*\nET\nendstrean\nendobj\n\n");

    //Length
    fwrite($pdf, $lengthNumber." 0 obj\n36\nendobj\n\n");
}

?>
