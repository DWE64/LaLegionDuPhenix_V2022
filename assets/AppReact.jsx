import React from 'react';
/*
import {ManageUser} from 'js/adminReact/ManageUser'
*/

class AppReact extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            ...this.props.state,
            isLoading: true
        };
    }
    render() {
        if(!this.state.isLoading){
            return (
                <div>
                    <p>Veuillez patienter svp...</p>
                    <p>Chargement de la page...</p>
                </div>
            );
        }else {
            return (
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-12">
                            <div className="page-title-box">
                                <h1 className="page-title">Liste des utilisateurs</h1>
                            </div>
                        </div>
                    </div>
{/*
                    <ManageUser state={this.state}/>
*/}

                </div>
            );
        }
    }
}

export default AppReact;