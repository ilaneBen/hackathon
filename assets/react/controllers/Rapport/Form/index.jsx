// Importez useEffect et useState depuis React
import React, { useState } from "react";
import Loader from "../../Loader";
import Button from "../../Components/Button";  // Assurez-vous d'importer votre composant Loader

export default function ({ closeRef, project, setRapports, rapport }) {
    const isEditing = !!rapport;
    const buttonText = isEditing ? "Modifier" : "Créer";
    const loadingButtonText = isEditing ? "Modification..." : "Création...";


    const [toolSettings, setToolSettings] = useState({
        useComposer: true,
        usePHPVersion: true,
        usePHPStan: true,
        usePHPCS: true,
        useStyleLine: true,
    });

    const [isLoading, setIsLoading] = useState(false);

    const handleChange = (tool) => {
        setToolSettings((prevSettings) => ({
            ...prevSettings,
            [tool]: !prevSettings[tool],
        }));
    };

    const submitForm = async (e) => {
        e.preventDefault();

        setIsLoading(true);

        const formData = new FormData(e.target);
        formData.set('useComposer', toolSettings.useComposer ? '1' : '0');
        formData.set('usePHPStan', toolSettings.usePHPStan ? '1' : '0');
        formData.set('usePHPCS', toolSettings.usePHPCS ? '1' : '0');
        formData.set('useStyleLine', toolSettings.useStyleLine ? '1' : '0');
        formData.set('usePHPVersion', toolSettings.usePHPVersion ='1');

        try {
            const response = await fetch(`/api/git/clone/${project.id}`, {

                method: "POST",
                body: formData,
            });

            const responseData = await response.json();

            console.log(responseData);

            if (responseData.code === 200) {
                console.log('La requête a réussi');
                // Assurez-vous que la réponse contient l'ID du rapport
                const rapportId = responseData.rapportId;
                closeRef.current.click();
                window.location.href = `/rapport/${rapportId}`;
            } else {
                console.error("La requête a échoué avec le code :", responseData.code);
            }
        } catch (error) {
            console.error("Erreur lors de la requête fetch :", error);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <form onSubmit={submitForm} className="modal-form">
            <hr/>
            <div className="d-flex justify-content-center">
                <h6>Analyse Composer</h6>
            </div>
            <div className="form-check form-switch">
                <label className="form-check-label" >
                    <input
                        className="form-check-input"
                        type="checkbox"
                        name="useComposer"
                        checked={toolSettings.useComposer}
                        onChange={() => handleChange('useComposer')}
                    />
                </label>
                Utiliser Composer Audit
            </div>
            <hr/>
            <div className="d-flex justify-content-center">
                <h6>Analyse PHP</h6>
            </div>
            <div className="form-check form-switch">
                <label className="form-check-label">
                    <input
                        className="form-check-input"
                        type="checkbox"
                        name="usePHPStan"
                        checked={toolSettings.usePHPStan}
                        onChange={() => handleChange('usePHPStan')}
                    />
                </label>
                Utiliser PHPStan
            </div>
            <div className="form-check form-switch">
                <label className="form-check-label">
                    <input
                        className="form-check-input"
                        type="checkbox"
                        name="usePHPCS"
                        checked={toolSettings.usePHPCS}
                        onChange={() => handleChange('usePHPCS')}
                    />
                </label>
                Utiliser PHPCS
            </div>
            <hr/>
            <div className="d-flex justify-content-center">
                <h6>Analyse CSS & SCSS</h6>
            </div>
            <div className="form-check form-switch">
                <label className="form-check-label">
                    <input
                        className="form-check-input"
                        type="checkbox"
                        name="useStyleLine"
                        checked={toolSettings.useStyleLine}
                        onChange={() => handleChange('useStyleLine')}
                    />
                </label>
                Utiliser Style Line
            </div>


            {/* Ajoutez des champs similaires pour d'autres outils */}

            <div className="form-group">
                <div className="form-group">
                    <Button text={buttonText} loadingText={loadingButtonText} isLoading={isLoading} />
                </div>
            </div>
        </form>
    );
}
