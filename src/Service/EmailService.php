<?php

// src/Service/EmailService.php

namespace App\Service;

use App\Entity\Project;
use App\Entity\Rapport;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    // Use "const" without the "$" sign for class constants
    public const MAIL_HOST = 'localhost';
    public const MAIL_PORT = 1025;

    // Use private properties for constants
    private $mailHost = self::MAIL_HOST;
    private $mailPort = self::MAIL_PORT;

    /**
     * Envoie un email contenant le rapport d'analyse à l'utilisateur associé au projet.
     *
     * @param Project $project le projet dont le rapport d'analyse sera envoyé
     * @param Rapport $rapport le rapport d'analyse à envoyer
     *
     * @return string un message indiquant si l'envoi a réussi ou s'il y a eu une erreur
     */
    public function sendEmail(Project $project, Rapport $rapport)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->mailHost;
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Your SMTP username
            $mail->Password = ''; // Your SMTP password
            $mail->Port = $this->mailPort;

            $mail->setFrom('codeScan@no-reply.fr', 'CodeScan-No-Reply');
            $mail->addAddress($project->getUser()->getEmail(), 'Rapport d\'analyse');
            $mail->isHTML(true);
            $mail->Subject = "Rapport d'analyse codeScan";
            $mail->Body = "Bonjour, voici le rapport d'analyse de projet ".$project->getName().' réalisé le '.
                $rapport->getDate()->format('d-m-Y').' à '.$rapport->getDate()->format('H:i:s').'<br><br>';
            $jobs = $rapport->getJob()->getValues();
            foreach ($jobs as $job) {
                $jobName = $job->getName();
                $jobResult = $job->isResultat() ? 'Aucun défaut trouvé.' : 'Plusieurs défauts trouvés. Invitez à lire le rapport d\'audit.';

                $mail->Body .= " $jobName <br>Résultat : $jobResult <br><br>";
            }
            $mail->Body .= "Les résultats de l'analyse sont disponibles à l'adresse : <a href='".$_ENV['SITE_BASE_URL'].'/rapport/'.$rapport->getId()."'>lien vers le rapport</a>";
            $mail->send();

            return 'Le message a été envoyé';
        } catch (\Exception $e) {
            return "Le message n'a pas pu être envoyé. Erreur du mailer : {$mail->ErrorInfo}";
        }
    }
}
