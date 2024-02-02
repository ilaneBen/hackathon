import React, { Fragment, useEffect, useState } from "react";
import { Bar } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';

export default function ({ message, dashboardUrl }) {

    const [data, setData] = useState([]);
    const [chartsOpt, setChartsOpt] = useState({});

    useEffect(() => {
        fetch(dashboardUrl, {
            method: "GET",
        })
            .then((res) => res.json())
            .then((res) => {
                console.log(res);
                if (res?.code === 200) {
                    setData([
                        {
                            ...res.data.projectsData,
                            'name': "projets",
                            'action': "scannés",
                            'icon': "github",
                            'iconColor': "dark",
                            'chartColor': "rgba(33, 37, 41, 0.8)",
                            'chart': true,
                        },
                        {
                            ...res.data.rapportsData,
                            'name': "rapports",
                            'action': "effectués",
                            'icon': "clipboard-data",
                            'iconColor': "danger",
                            'chartColor': "rgba(220, 53, 69, 0.8)",
                            'chart': true,
                        },
                        {
                            ...res.data.jobsData,
                            'name': "jobs",
                            'action': "complétés",
                            'icon': "speedometer",
                            'iconColor': "success",
                            'chartColor': "rgba(25, 135, 84, 0.8)",
                            'chart': true,
                        },
                        {
                            ...res.data.usersData,
                            'name': "utilisateurs",
                            'action': "inscrits",
                            'icon': "people-fill",
                            'iconColor': "primary",
                            'chart': false,
                        },
                    ]);
                }
            });
    }, []);

    useEffect(() => {
        setTimeout(() => {
            document.querySelectorAll('.card.stats').forEach(el => {
                el.classList.add('pop');
            });

            document.querySelectorAll('.card.charts, .card.podium').forEach(el => {
                el.classList.add('show');
            });

        }, 100);

        ChartJS.register(
            CategoryScale,
            LinearScale,
            BarElement,
            Title,
            Tooltip,
            Legend
        );


        let tabCharts = {
            data: {
                labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                datasets: data.filter((object) => object.chart).map(
                    (typeData) => {
                        return {
                            label: typeData.name.charAt(0).toUpperCase() + typeData.name.slice(1) + " " + typeData.action,
                            data: Object.values(typeData.countSorted),
                            borderWidth: 1,
                            backgroundColor: typeData.chartColor,
                        }
                    }
                )
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                ticks: {
                    precision: 0
                },
                plugins: {
                    title: {
                        display: true,
                        text: `Données de l'année ` + (new Date()).getFullYear(),
                    },
                }
            }
        };
        setChartsOpt(tabCharts);

        setTimeout(() => {
            document.querySelectorAll('.card.podium .bar').forEach(el => {
                el.classList.add('show');
                el.classList.add('grow');
            });
        }, 500);

    }, [data]);

    return (
        <div id="dashboard">
            <h1 className="dashboard-title">Dashboard</h1>
            <div className="row">
                {
                    data.map((typeData) => (
                        <Fragment key={typeData.name}>
                            <div className="col-lg-3 col-sm-5 col-xs-12">
                                <div className="card stats m-2">
                                    <div className="card-body">
                                        <div className="row">
                                            <div className="col">
                                                <h5 className="card-title text-uppercase text-muted mb-0">{typeData.name.charAt(0).toUpperCase() + typeData.name.slice(1)}</h5>
                                                <span className="h2 font-weight-bold mb-0">{typeData.nbObjects}</span>
                                            </div>
                                            <div className="col-4">
                                                <div className={"icon icon-shape bg-" + typeData.iconColor + " text-white rounded-circle shadow"}>
                                                    <i className={"bi bi-" + typeData.icon}></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Fragment>
                    ))
                }
            </div>
            <div className="row">
                <div className="card charts fade">
                    <div className="card-body">
                        {
                            chartsOpt.options && chartsOpt.data ? (
                                <Bar
                                    options={chartsOpt.options}
                                    data={chartsOpt.data}
                                />
                            ) : ""
                        }
                    </div>
                </div>
            </div>
            <div className="row">
                {
                    data.filter((object) => !object.chart).map((typeData) => (
                        <Fragment key={typeData.name}>
                            <div className="card podium fade">
                                <div className="card-body text-center">
                                    <div className="card-title">
                                        <h4>Classment d'utilisation (rapports effectués)</h4>
                                    </div>
                                    <div className="scoreboard row">
                                        {
                                            typeData.podium.map((user) => (
                                                <Fragment key={user.email}>
                                                    <div className="col-3 bar-div">
                                                        <p className="rapport-count">{user.countRapport}</p>
                                                        <div className={"bar fade " + user.place}>
                                                            {user.placeNumber}
                                                        </div>
                                                        <p>{user.name} <br /> <small>{user.email}</small></p>
                                                    </div>
                                                </Fragment>
                                            ))
                                        }
                                    </div>
                                </div>
                            </div>
                        </Fragment>
                    ))
                }

            </div>
        </div >
    );
}
