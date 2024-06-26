import React from 'react';
import { APICore } from '../api/apiCore';

class ManageUser extends React.Component {
    constructor(props) {
        super(props);
        this.api = new APICore();
        this.state = {
            users: this.props.state.users,
            isSuperAdmin: this.props.state.isSuperAdmin
        };
    }

    handleSwitchChange = (userId, url) => {
        this.api.update(url, { id: userId }).then((response) => {
            const updatedUser = response.data;
            this.setState((prevState) => {
                const updatedUsers = prevState.users.map((user) => {
                    if (user.id === userId) {
                        return {
                            ...user,
                            associationRegistrationDate: updatedUser.associationRegistrationDate,
                            updatedAt: updatedUser.updatedAt,
                            memberStatus: updatedUser.memberStatus
                        };
                    }
                    return user;
                });
                return { users: updatedUsers };
            });
        }).catch((error) => {
            console.error('Changement non effectué:', error);
        });
    };

    handleDeleteUser = (userId, url) => {
        if (confirm('Are you sure you want to delete this user?')) {
            this.api.delete(url).then(() => {
                this.setState((prevState) => ({
                    users: prevState.users.filter(user => user.id !== userId)
                }));
            }).catch((error) => {
                console.error('Échec de la suppression:', error);
            });
        }
    };

    handleEditUser = (userId, url, data) => {
        this.api.update(url, data).then((response) => {
            const updatedUser = response.data;
            this.setState((prevState) => {
                const updatedUsers = prevState.users.map((user) => {
                    if (user.id === userId) {
                        return {
                            ...user,
                            ...updatedUser
                        };
                    }
                    return user;
                });
                return { users: updatedUsers };
            });
        }).catch((error) => {
            console.error('Échec de la mise à jour:', error);
        });
    };

    render() {
        const { users, isSuperAdmin } = this.state;

        return (
            <div className="tab-content">
                <div className="tab-pane show active" id="basic-datatable-preview">
                    <table id="basic-datatable" className="table dt-responsive nowrap w-100">
                        <thead>
                        <tr>
                            <th>Email</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            {isSuperAdmin && <th>Rôle</th>}
                            <th>Statut du membre</th>
                            <th>Date d'inscription</th>
                            <th>Membre</th>
                            <th>Date de création</th>
                            <th>Date de mise à jour</th>
                            <th>Nom d'utilisateur</th>
                            <th>Date de naissance</th>
                            <th>Adresse</th>
                            <th>Ancienneté</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        {users.map((user) => (
                            <tr key={user.id} className={`user-row-${user.id}`}>
                                <td>{user.email}</td>
                                <td className={`user-name-${user.id}`}>{user.name}</td>
                                <td className={`user-firstname-${user.id}`}>{user.firstname}</td>
                                {isSuperAdmin && (
                                    <td>
                                        <p className={`user-roles-${user.id}`}>
                                            {user.roles.join(', ')}
                                        </p>
                                        <button type="button" className="btn btn-outline-primary btn-sm btn-modal" data-bs-toggle="modal" data-bs-target={`#user-manage-role-modal-${user.id}`} data-id={user.id}>
                                            <i className="mdi mdi-pencil-outline"></i>
                                        </button>
                                    </td>
                                )}
                                <td className={`user-memberStatus-${user.id}`}>{user.memberStatus}</td>
                                <td className={`user-associationRegistrationDate-${user.id}`}>{user.associationRegistrationDate ? new Date(user.associationRegistrationDate).toLocaleDateString() : '-'}</td>
                                <td>
                                    <div>
                                        <input type="checkbox" id={`switch${user.id}`}
                                               name={`switch-button-user-${user.id}`} defaultChecked={user.isAssociationMember}
                                               data-switch="success"
                                               data-url={`/admin/manager/user/${user.id}/change_status_is_association_member`}
                                               className="switch_is_member_association"
                                               onChange={() => this.handleSwitchChange(user.id, `/admin/manager/user/${user.id}/change_status_is_association_member`)}
                                        />
                                        <label htmlFor={`switch${user.id}`} data-on-label="Yes" data-off-label="No"
                                               className="mb-0 d-block"></label>
                                    </div>
                                </td>
                                <td>{new Date(user.createdAt).toLocaleDateString()}</td>
                                <td className={`user-updatedAt-${user.id}`}>{user.updatedAt ? new Date(user.updatedAt).toLocaleDateString() : '-'}</td>
                                <td className={`user-username-${user.id}`}>{user.username}</td>
                                <td className={`user-birthday-${user.id}`}>{user.birthday ? new Date(user.birthday).toLocaleDateString() : '-'}</td>
                                <td className={`user-address-${user.id}`}>{user.address} - {user.postalCode} {user.city}</td>
                                <td className={`user-member-seniority-${user.id}`}>{user.memberSeniority || '-'}</td>
                                <td>
                                    <button type="button" className="btn btn-outline-primary btn-sm btn-modal" data-bs-toggle="modal" data-bs-target={`#user-manage-modal-${user.id}`} data-id={user.id}>
                                        <i className="mdi mdi-pencil-outline"></i>
                                    </button>
                                    {isSuperAdmin && (
                                        <button type="button" className="btn btn-danger btn-sm" onClick={() => this.handleDeleteUser(user.id, `/admin/manager/user/${user.id}/delete`)}>
                                            <i className="mdi mdi-delete"></i>
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
            </div>
        );
    }
}

export {ManageUser};
