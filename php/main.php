<?php

include "dbConnect.php";
include_once "fpdf186/fpdf.php";

class pdf extends fpdf
{
    function Header()
    {
        $this->Image('logo.png',80, 5, 50);
        $this->Ln(20);
        $this->SetFont('Arial','B',13);
        $this->Cell(80);
        $this->Ln(30);
        $this->Cell(0, 10, 'Student Transcript', 1, 1, 'C');
        $this->Ln(15);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(190,10,'Page '.$this->PageNo().' out of {nb}',0,0,'C');
    }

}

$pdf = new pdf();

$pdf-> AddPage();
$pdf->SetFont('Arial','',14);

$allStudents=mysqli_query($conn,"SELECT * from student");
$numberOfStudents = mysqli_num_rows($allStudents);
$count = 0;

$display_heading = array('Course Name', 'Grade Obtained', 'Term Name');

while($data=mysqli_fetch_array($allStudents,MYSQLI_ASSOC))
{
    $count ++;
    $pdf->SetFont('Arial','B',13);
    $pdf->Cell(100,7,"Student Name : ".$data['firstName']." ".$data['lastName'],0,0,'L');
    $pdf->Cell(90,7,"Date : ".date("Y/m/d"),0,0,'R');
    $pdf->Ln(7);
    $pdf->Cell(100,7,"Enrollment Number : 234567",0,0,'L');
    $pdf->Ln(15);
    $allData = mysqli_query($conn,"SELECT Student.firstName,
    Student.lastName,
    Course.course_title,
    Enrollment.grade,
    Term.term_name
    FROM
    Student
    JOIN Enrollment ON Student.student_id = Enrollment.student_id
    JOIN Course ON Course.course_id = Enrollment.course_id
    JOIN Term ON Term.term_name = Enrollment.term_name
    where Student.student_id = $data[student_id]");

    for($i=0;$i<sizeof($display_heading);$i++)
    {
        $pdf->SetFont('Arial','B',13);
        $pdf->SetFillColor(204,255,255);
        $pdf->Cell(63,10,$display_heading[$i],1,0,'C',true);
    }
    
    $pdf->Ln();
    while($individualData=mysqli_fetch_array($allData,MYSQLI_ASSOC))
    {
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(63,8,$individualData['course_title'],1,0,'C');
        $pdf->Cell(63,8,$individualData['grade'],1,0,'C');
        $pdf->Cell(63,8,$individualData['term_name'],1,0,'C');
        $pdf->Ln();
    }
    $pdf->Ln(50);
    $pdf->Cell(192,1,'____________________ ',0,0,'R');
    $pdf->Ln(4);
    $pdf->Cell(177,7,'Signature',0,0,'R');
    if($count==$numberOfStudents)
    {
        break;
    }
    $pdf->AddPage(); 
}   

$pdf->AliasNbPages();
$pdf->Output();

?>