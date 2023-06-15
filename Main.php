<?php

// Read in CSV file
$csvFile = 'C:\Users\Andrew\Documents\GitHub\phpCsvToPdfNoLibraries\L&M2023-04-27 14_17_11.csv';

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
$fileLength = $file->key() /40;
$pages = ceil($fileLength);
echo $pages;
$allPages = $pages;
$content = 0;

//splitting up the csv into chunks that will fit on each page
$chunk_size = 40;
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
$rows = str_replace(",", "   |   ", $rows);
$rows = str_replace("\"", "", $rows);
$rows = str_replace("| 	 | 	 | 	 |  |  |  |  |  |", "", $rows);
$rows = str_replace(" |  |  |  |  |  |  |  |  | ", "", $rows);
$rows = str_replace(" |  |  | ", "|", $rows);
$rows = str_replace("out) ", "out)\n\n", $rows);
$rows = str_replace("|   	   |   	   |   	   |      |      |      |      |      |", "", $rows);
$rows = str_replace("|      |      |      |      |      |      |      |      |", "", $rows);
$rows = str_replace("|      |      |", "|", $rows);

// Set output file name
$pdfFile = $fullFileName;


// Open new PDF file for writing
$pdf = fopen($pdfFile, "r+");

//Setting variables that will be used to build the PDF elements
$objNum = 1;   
$versionNum = 0;
$pageNum = $pages;
$space = " ";
$pageSize = " /MediaBox [0 0 792 612 ]";
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
$stream = "<< >>".$lineFeed."stream".$lineFeed."BT".$lineFeed." /F0 12 Tf".$lineFeed."40 585 Td".$lineFeed;
$endstream = "ET".$lineFeed."endstream".$lineFeed;
$catalog = 5;
$eof = ">>".$lineFeed."startxref".$lineFeed."%%EOF";
$kids = array();
$kids[] = "/Kids [2 0 R";

//Header
echo "writing header";
fwrite($pdf, "");
fwrite($pdf, "%PDF-2.0".$lineFeed."%äãÏÒ".$lineFeed);

//Page Tree (Parent)
fwrite($pdf, $parent.$space.$versionNum.$space.$o);
fwrite($pdf, "<</Type /Pages".$lineFeed);
fwrite($pdf, " /Count ".$pageNum.$lineFeed);
fwrite($pdf, " /Kids [".$objNum+1..$space."                                                                                                                                                                                                        >>".$lineFeed.$eo);

//Object 2 aka Page 1
echo "writing page 1";
fwrite($pdf, "2 0 obj\n<< /Type /Page\n/MediaBox [0 0 792 612]\n/Resources 3 0 R\n/Parent 1 0 R\n/Contents [4 0 R]\n>>\nendobj\n\n");

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
    while ($rowCount <= 40){
fwrite($pdf, "(".$rows[$rowCount].")".$textPaint.$return.$lineFeed);
 $rowCount++;
}}

//Ending the object
fwrite($pdf, $endstream.$eo);

//Setting the variables for pages after page 1
$counter = 2;
$count = 0;
$objNumber = 7;
$contNumber = 8;
$lengthNum = 9;
$refNum1 = 41;
$refNum2 = 81;

//Creating the pages
while ($pages > 1){
    
     //Page Setup
     fwrite($pdf, $objNumber." 0 obj\n");
     fwrite($pdf, "<< /Type /Page\n/MediaBox [0 0 792 612]\n/Resources 3 0 R\n/Parent 1 0 R\n/Contents [".$contNumber." 0 R]\n>>\n");
     fwrite($pdf, "endobj\n\n");
     $kids[] = $objNumber." 0 R";
     
    //Contents
    fwrite($pdf, $contNumber." 0 obj\n<</Length ".$lengthNum.">>\nstream\nBT\n /F0 12 Tf\n40 575 Td\n14 TL\n");

    //Paginating the data onto the pages
    while ($rowCount <= $refNum2 && $rowCount <= $totalRows){
               
        fwrite($pdf, "(".$rows[$rowCount].")".$textPaint.$return.$lineFeed);
       // echo $rowCount." ";
        $rowCount++;       

    }    
    echo "writing page ".$counter."\n";
    
    //Moving onto addional objects
    $objNumber = $objNumber + 3;
    $contNumber = $contNumber + 3;
    $lengthNum = $lengthNum + 3;
    $pages--;
    $refNum2 = $refNum2 + 40;
    $counter++;
    
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
echo "Writing Trailer";
fwrite($pdf, "trailer".$lineFeed);
fwrite($pdf, "<< /Root ".$catalog.$space.$versionNum.$space."R".$lineFeed);
fwrite($pdf, $eof);


//Inserting additional objects (visible pages) to Kids to be displayed
echo "Changing Kids";

//Close off the kids and linefeed
$kids[] = "]\n";

//Substr_replace can't work with an array, implode() converts the array to a string
$impKids = implode(" ", $kids);

//putting the resource into a string
$exploded = fgets($pdf);

//Substr_replace can't work with a resource, explode() converts the resource into a string
$explode = explode(",",$exploded);

//Replacing the old kids with the new kids
$newPdf = substr_replace($explode, $impKids, 40, 0);

//Imploding the new strings together
$newPdf = implode(" ", $newPdf);

//Seeking to where to put the new kids string
fseek($pdf, 54);

//Writing to file
fwrite($pdf, $newPdf);

?>
