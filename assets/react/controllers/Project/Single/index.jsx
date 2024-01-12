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
                            <th>ID du Rapport</th>
                            <th></th>
                            <th>Voir</th>
                        </tr>
                        </thead>
                        <tbody>
                        {project.rapport.map((report) => (
                            <tr key={report.id}>
                                <td>{report.id}</td>
                                <td>{report.content}</td>
                                <td className="d-flex">
                                    <a href={report.showUrl} className="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 20 20" height="20" fill="none" className="svg-icon"><g stroke-width="1.5" stroke-linecap="round" stroke="#5d41de"><circle r="2.5" cy="10" cx="10"></circle><path fill-rule="evenodd" d="m8.39079 2.80235c.53842-1.51424 2.67991-1.51424 3.21831-.00001.3392.95358 1.4284 1.40477 2.3425.97027 1.4514-.68995 2.9657.82427 2.2758 2.27575-.4345.91407.0166 2.00334.9702 2.34248 1.5143.53842 1.5143 2.67996 0 3.21836-.9536.3391-1.4047 1.4284-.9702 2.3425.6899 1.4514-.8244 2.9656-2.2758 2.2757-.9141-.4345-2.0033.0167-2.3425.9703-.5384 1.5142-2.67989 1.5142-3.21831 0-.33914-.9536-1.4284-1.4048-2.34247-.9703-1.45148.6899-2.96571-.8243-2.27575-2.2757.43449-.9141-.01669-2.0034-.97028-2.3425-1.51422-.5384-1.51422-2.67994.00001-3.21836.95358-.33914 1.40476-1.42841.97027-2.34248-.68996-1.45148.82427-2.9657 2.27575-2.27575.91407.4345 2.00333-.01669 2.34247-.97026z" clip-rule="evenodd"></path></g></svg>
                                        <span className="lable">Voir le rapport</span>
                                    </a>
                                    <form className="form-show" action={`/delete_project/${project.id}`} method="post">
                                    <button type="submit" className="button-delete">
                                        <svg viewBox="0 0 448 512" className="svgIcon"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path></svg>
                                    </button>
                                </form>
                                </td>
                                <td>


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
            <a className="button-repport" href="/project_index">Retour à la liste</a>

            <a className="button-repport" href={`/project_edit/${project.id}`}>Modifier</a>

            {project.url && (
                <a className="button-repport" href={`/api_git_clone/${project.id}`}>Lancer le Test</a>
            )}

            </div>
        </div>
    );
};

export default ProjectDetails;

