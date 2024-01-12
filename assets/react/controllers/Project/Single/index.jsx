import React from 'react';
const ProjectDetails = ({ project, }) => {
    
    return (
        <div className="m-2">
            <h1>Projet {project.name}</h1>
            <h2 className="bg-success"></h2>
            <table className="">
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
                                        <i class="bi bi-gear svg-icon"></i>
                                            <span className="lable">Voir le rapport</span>
                                        </a>
                                        <form className="form-show" action={`/delete_project/${project.id}`} method="post">
                                            <button type="submit" className="button-delete">
                                                <i className="bi bi-trash3 svgIcon"></i>
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

            <a className="button-repport" href={project.cloneUrl}>Lancer le Test</a>

            </div>
        </div>
    );
};

export default ProjectDetails;

