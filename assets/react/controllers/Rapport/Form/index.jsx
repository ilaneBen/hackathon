// Importez useEffect et useState depuis React
import React, { useState } from "react";

export default function ({ closeRef, project, setRapports, rapport }) {
    // Nouvel état pour les outils
    const [toolSettings, setToolSettings] = useState({
        useComposer: true,
        usePHPVersion: true,
        usePHPStan: true,
        usePHPCS: true,
    });

    const handleChange = (tool) => {
        setToolSettings((prevSettings) => ({
            ...prevSettings,
            [tool]: !prevSettings[tool],
        }));
    };

    const submitForm = async (e) => {
        e.preventDefault();
        console.log(e.target);
            const formData = new FormData(e.target);
            // Assurez-vous que les noms des champs correspondent aux noms dans Symfony
            formData.set('useComposer', toolSettings.useComposer ? '1' : '0');
            formData.set('usePHPStan', toolSettings.usePHPStan ? '1' : '0');
            formData.set('usePHPCS', toolSettings.usePHPCS ? '1' : '0');
            formData.set('usePHPVersion', toolSettings.usePHPVersion ? '1' : '0');

            // const response = await fetch(`/api/git/clone/${project.id}`, {
            //     method: "POST",
            //     body: formData,
            // });

        fetch(`/api/git/clone/${project.id}`, {
            method: "POST",
            body: formData,
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                   window.location.href =`/rapport/${res.rapportId}`;
                }
            })
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
                <button type="submit" className="btn btn-primary">
                    Envoyer
                </button>
            </div>
        </form>
    );
}
