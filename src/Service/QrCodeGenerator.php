<?php

namespace App\Service;

use App\Entity\Voyage;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeGenerator
{
    public function createQrCode(Voyage $voyage): ResultInterface
    {
        // Generate vCard content
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "DESTINATION:" . $voyage->getDestination() . "\n";
        $vcard .= "IMAGE:" . $voyage->getImage() . "\n";
        $vcard .= "PRIX:" . $voyage->getPrix() . "\n";
        $vcard .= "PROGRAMME:" . $voyage->getProgramme() . "\n";
        $vcard .= "DATEDEPART:" . $voyage->getDateDepart()->format('Y-m-d H:i:s') . "\n";
        $vcard .= "DATEARRIVE:" . $voyage->getDateArrive()->format('Y-m-d H:i:s') . "\n";
        // Add more fields as needed

        // End vCard
        $vcard .= "END:VCARD\n";

        // Generate the QR code with vCard data
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->writerOptions([])
            ->data($vcard)
            ->encoding(new Encoding('UTF-8'))
            //  ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->labelText('Scan to view contact information')
            ->labelFont(new NotoSans(20))
            ->validateResult(false)
            ->build();

        return $result;
    }
}
