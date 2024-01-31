// Importez useEffect et useState depuis React
import React, { useState } from "react";
import Loader from "../../Loader";  // Assurez-vous d'importer votre composant Loader

export default function ({ closeRef, project, setRapports, rapport }) {
    // Nouvel état pour les outils et le chargement
    const [toolSettings, setToolSettings] = useState({
        useComposer: true,
        usePHPVersion: true,
        usePHPStan: true,
        usePHPCS: true,
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

        setIsLoading(true);  // Activer le chargement au début de la requête

        const formData = new FormData(e.target);
        formData.set('useComposer', toolSettings.useComposer ? '1' : '0');
        formData.set('usePHPStan', toolSettings.usePHPStan ? '1' : '0');
        formData.set('usePHPCS', toolSettings.usePHPCS ? '1' : '0');
        formData.set('usePHPVersion', toolSettings.usePHPVersion ? '1' : '0');

        fetch(`/api/git/clone/${project.id}`, {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                    window.location.href = `/rapport/${res.rapportId}`;
                }
            })
            .catch((error) => {
                console.error("Erreur lors de la requête fetch :", error);
            })
            .finally(() => {
                setIsLoading(false);  // Désactiver le chargement à la fin de la requête
            });
    };

    return (
        <form onSubmit={submitForm} className="modal-form">
            {/* Nouveaux champs de formulaire pour les outils */}
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
            <div className="form-check form-switch">
                <label className="form-check-label">
                    <input
                        className="form-check-input"
                        type="checkbox"
                        name="usePHPVersion"
                        checked={toolSettings.usePHPVersion}
                        onChange={() => handleChange('usePHPVersion')}
                    />
                </label>
                Utiliser PHP Version
            </div>

            {/* Ajoutez des champs similaires pour d'autres outils */}

            <div className="form-group">
                <button type="submit" className="btn btn-primary" disabled={isLoading}>
                    {isLoading ? <Loader /> : "Envoyer"}
                </button>
            </div>
        </form>
    );
}
