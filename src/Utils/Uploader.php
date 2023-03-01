<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    public function upload(UploadedFile $file, string $directory, string $name = ""){
        //1 - Crée un nom au document téléchargé (avec un ID aléatoire généré + concat de l'extension)
        $newFileName = $name . '-' . uniqid() . '.' . $file->guessExtension();

        //2 - Déplace le fichier chargé dans le répertoire indiqué et renommé avec le $newFileName
        $file->move($directory, $newFileName);

        //3 - Renvoie le nom du fichier créé
        return $newFileName;
    }
}