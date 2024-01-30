import React, {useRef, useState} from 'react';
import Modal from "../../Components/Modal/index.jsx";
import Form from "../../Rapport/Form";
import Delete from "../Delete";

const ProjectDetails = ({ project }) => {
    const [report, setReport] = useState(null);
    const [deleteCsrf, setDeleteCsrf] = useState("");
    const [isForm, setIsForm] = useState(false);
    const handleDelete = (report) => {
        setIsForm(false);
        setDeleteCsrf(report.deleteCsrf);
        setReport(report);
    };

    const closeRef = useRef();
    const chooseTest = ()=>{setIsForm(true)};

    const deleteTest = ()=>{setIsForm(false)
    };

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

            {project.rapport && project.rapport.length !== 0 ? (
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
                        {project.rapport.map((report) => (
                            <tr key={report.id}>
                                <td styles="max-width: 5%; min-width: 5%;">{report.id}</td>
                                <td styles="max-width: 95%; min-width: 95%;">
                                    <div className="d-flex">
                                        <a href={report.showUrl} className="button">
                                        <i className="bi bi-gear svg-icon"></i>
                                            <span className="lable">Voir le rapport</span>
                                        </a>
                                        <form className="form-show" onSubmit={() => {}} method="post">
                                            <button
                                                type="button"
                                                className="button-delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#projectModal"
                                                onClick={deleteTest}
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
                        closeRef={closeRef}
                        project={project}
                    />
                ) : (
                    // <Delete closeRef={closeRef} project={project} csrf={deleteCsrf} />*
                    <p>test</p>
                )}
            </Modal>
        </div>
    );
};

export default ProjectDetails;

