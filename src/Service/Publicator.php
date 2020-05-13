<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Publicator
{
    private $targetDirectory;
    private $logo;

    public function __construct($targetDirectory, $logo)
    {
        $this->targetDirectory = $targetDirectory;
        $this->logo = $logo;
    }

    public function toPost(string $path): string
    {
        $originalFilename = pathinfo($path, PATHINFO_FILENAME);
        $originalExtension = pathinfo($path, PATHINFO_EXTENSION );
        $rutaImagenOriginal = $path;
        $rutaMarcaDeAgua = $this->getLogo();

        switch ($originalExtension)
        {
            case 'png' : $original = imagecreatefrompng($rutaImagenOriginal);break;
            case 'jpeg': $original = imagecreatefromjpeg($rutaImagenOriginal);break;
            case 'jpg' : $original = imagecreatefromjpeg($rutaImagenOriginal);break;
            case 'webp': $original = imagecreatefromwebp($rutaImagenOriginal);
        }

        $marcaDeAgua = imagecreatefrompng($rutaMarcaDeAgua);

        # Necesitamos sacar antes las anchuras y alturas
        $anchuraOriginal = imagesx($original);
        $alturaOriginal = imagesy($original);
        $alturaMarcaDeAgua = imagesy($marcaDeAgua);
        $anchuraMarcaDeAgua = imagesx($marcaDeAgua);

        # En dónde poner la marca de agua sobre la original
        $xOriginal = 0;
        $yOriginal = $alturaOriginal - $alturaMarcaDeAgua;

        $xMarcaDeAgua = 0;
        $yMarcaDeAgua = 0;
        $alturaMarcaDeAgua = imagesy($marcaDeAgua) - $yMarcaDeAgua;
        $anchuraMarcaDeAgua = imagesx($marcaDeAgua) - $xMarcaDeAgua;
        $porcentajeOpacidad = 30;

        imagecopymerge($original, $marcaDeAgua, $xOriginal, $yOriginal,
            $xMarcaDeAgua, $yMarcaDeAgua, $anchuraMarcaDeAgua, $alturaMarcaDeAgua, $porcentajeOpacidad);

        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename . '-' . uniqid().'.'.$originalExtension;
        imagepng($original, $this->targetDirectory.'/'.$fileName);

        return $fileName;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

}