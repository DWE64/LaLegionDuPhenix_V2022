import React from 'react';
import { ManageUser } from './js/adminReact/ManageUser';
import { UserApi } from "./js/api/userApi";

class AppReact extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            ...this.props.state,
            isLoading: false,
            users: []  // Initialize users state
        };
        this.apiUser = new UserApi();
    }

    componentDidMount() {
        if (this.state.role === "ROLE_STAFF" || this.state.role === "ROLE_SUPER_ADMIN") {
            this.apiUser.getUsers().then((data) => {
                const parsedData = JSON.parse(data.data);  // Parse the JSON string
                this.setState({
                    users: parsedData.listUsers,
                    listRole: parsedData.allRoles,
                    listSeniority: parsedData.seniorityStatus,
                    isLoading: true
                });
            }).catch((error) => {
                console.log(error);
            });
        }
    }

    render() {
        if (!this.state.isLoading) {
            return (
                <div>
                    <p>Veuillez patienter svp...</p>
                    <p>Chargement de la page...</p>
                </div>
            );
        } else if (!(this.state.role === "ROLE_STAFF" || this.state.role === "ROLE_SUPER_ADMIN")) {
            return (
                <div>
                    <p>Accès refusé !</p>
                    <p>Droit d'accès insuffisant !</p>
                </div>
            );
        } else {
            return (
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-12">
                            <div className="page-title-box">
                                <h1 className="page-title">Liste des utilisateurs</h1>
                            </div>
                        </div>
                    </div>
                    <ManageUser state={this.state} />
                </div>
            );
        }
    }
}

export { AppReact };
