import React, {useRef, useState} from 'react';
import Modal from "../../Components/Modal/index.jsx";
import Form from "../../Rapport/Form";
import Delete from "../../Rapport/Delete";

const ProjectDetails = ({ project }) => {
    const [rapports, setRapports] = useState(project.rapport);
    const [rapport, setRapport] = useState(null);
    const [deleteCsrf, setDeleteCsrf] = useState("");
    const [isForm, setIsForm] = useState(false);
    const handleDelete = (rapport) => {
        setIsForm(false);
        setDeleteCsrf(rapport.deleteCsrf);
        setRapport(rapport);
    };

    const closeRef = useRef();
    const chooseTest = ()=>{setIsForm(true);};
    console.log(rapports);
    return (
        <div className="m-2">
            <div className="d-flex">
            <h1>Rapports du Projet {project.name}</h1>
            <img className="img-report" src="/img/monitor.png"/>
            </div>
            <h2 className="bg-success"></h2>
            <table className="table table-striped-columns">
                <tbody>
                <tr>
                    <th>URL :</th>
                    <td>{project.url}</td>
                </tr>
                <tr>
                    <th>Statut</th>
                    <td>{project.statut ? 'Oui' : 'Non'}</td>
                </tr>
                </tbody>
            </table>

            {rapports && rapports.length !== 0 ? (
                <div>
                    <h2>Rapports du Projet</h2>
                    <table className="table">
                        <thead>
                        <tr>
                            <th styles="max-width: 5%; min-width: 5%;">ID</th>
                            <th styles="max-width: 95%; min-width: 95%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {rapports.map((rapport) => (
                            <tr key={rapport.id}>
                                <td styles="max-width: 5%; min-width: 5%;">{rapport.id}</td>
                                <td styles="max-width: 95%; min-width: 95%;">
                                    <div className="d-flex">
                                        <div className="d-flex align-items-center">
                                        <a href={rapport.showUrl} className="button">
                                        <i className="bi bi-gear svg-icon"></i>
                                            <span className="lable">Voir le rapport</span>
                                        </a>
                                        </div>
                                        <form className="form-show" onSubmit={() => {}} method="POST">
                                            <button
                                                type="button"
                                                className="button-delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#projectModal"
                                                onClick={()=> handleDelete(rapport)}
                                            >
                                                <svg viewBox="0 0 448 512" className="svgIcon"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path></svg>
                                            </button>

                                        </form>
                                    </div>
                                </td>
                            </tr>

                        ))}

                        </tbody>
                    </table>
                </div>
            ) : (
                <p>Aucun rapport disponible pour ce projet.</p>
            )}
            <div className="mb-2 d-flex">
            <a className="button-repport" href={project.indexUrl}>Retour à la liste</a>

            {/* <a className="button-repport" href={project.editUrl}>Modifier</a> */}

                <button
                    className="button-repport2"
                    data-bs-toggle="modal"
                    data-bs-target="#projectModal"
                    onClick={chooseTest}
                >
                    Lancer le Test
                </button>

            </div>
            <Modal
                closeRef={closeRef}
                title={isForm ? "creer un rapport" : "supprimer un rapport"}
            >
                {isForm ? (
                    <Form
                        rapport={rapport}
                        closeRef={closeRef}
                        rapports={rapports}
                        project={project}
                        setRapports={setRapports}
                    />
                ) : (
                    <>
                        <Delete closeRef={closeRef}
                                rapport={rapport}
                                csrf={deleteCsrf}
                                setRapports={setRapports}
                        />

                    </>
                )}
            </Modal>
        </div>
    );
};

export default ProjectDetails;

