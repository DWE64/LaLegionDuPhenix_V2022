import React from 'react';
import { UserApi } from '../api/userApi';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';

class ManageUser extends React.Component {
    constructor(props) {
        super(props);
        this.userApi = new UserApi();
        this.state = {
            users: this.props.state.users || [],
            isSuperAdmin: this.props.role === "ROLE_SUPER_ADMIN",
            sortConfig: {
                key: null,
                direction: 'asc'
            }
        };
    }

    handleSwitchChange = (userId, url) => {
        this.userApi.updateUser(url, { id: userId }).then((response) => {
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

    handleDeleteUser = (userId) => {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            this.userApi.deleteUser(userId).then(() => {
                this.setState((prevState) => ({
                    users: prevState.users.filter(user => user.id !== userId)
                }));
            }).catch((error) => {
                console.error('Échec de la suppression:', error);
            });
        }
    };

    handleEditUser = (userId, data) => {
        const url = `/api/admin/manage/user/${userId}/edit`;
        this.userApi.updateUser(url, data).then((response) => {
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

    handleSort = (key) => {
        let direction = 'asc';
        if (this.state.sortConfig.key === key && this.state.sortConfig.direction === 'asc') {
            direction = 'desc';
        }
        const sortedUsers = [...this.state.users].sort((a, b) => {
            if (a[key] < b[key]) {
                return direction === 'asc' ? -1 : 1;
            }
            if (a[key] > b[key]) {
                return direction === 'asc' ? 1 : -1;
            }
            return 0;
        });
        this.setState({
            users: sortedUsers,
            sortConfig: {
                key,
                direction
            }
        });
    };

    render() {
        const { users, isSuperAdmin, sortConfig } = this.state;

        if (!users || users.length === 0) {
            return <div>Aucun utilisateur disponible</div>;
        }

        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12">
                        <div className="page-title-box">
                            <h1 className="page-title">Liste des utilisateurs</h1>
                        </div>
                    </div>
                </div>
                <div className="tab-content">
                    <div className="tab-pane show active" id="basic-datatable-preview">
                        <div className="table-responsive">
                            <table id="basic-datetable" className="table dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th onClick={() => this.handleSort('email')}>Email {sortConfig.key === 'email' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('name')}>Nom {sortConfig.key === 'name' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('firstname')}>Prénom {sortConfig.key === 'firstname' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    {isSuperAdmin && <th onClick={() => this.handleSort('roles')}>Rôle {sortConfig.key === 'roles' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>}
                                    <th onClick={() => this.handleSort('memberStatus')}>Statut du membre {sortConfig.key === 'memberStatus' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('associationRegistrationDate')}>Date d'inscription {sortConfig.key === 'associationRegistrationDate' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th>Membre</th>
                                    <th onClick={() => this.handleSort('createdAt')}>Date de création {sortConfig.key === 'createdAt' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('updatedAt')}>Date de mise à jour {sortConfig.key === 'updatedAt' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('username')}>Nom d'utilisateur {sortConfig.key === 'username' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('birthday')}>Date de naissance {sortConfig.key === 'birthday' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('address')}>Adresse {sortConfig.key === 'address' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th onClick={() => this.handleSort('memberSeniority')}>Ancienneté {sortConfig.key === 'memberSeniority' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                {users.map((user) => (
                                    <tr key={user.id} className={`user-row-${user.id}`}>
                                        <td>{user.email}</td>
                                        <td className={`user-name-${user.id}`}>{user.name}</td>
                                        <td className={`user-firstname-${user.id}`}>{user.firstname}</td>
                                        {isSuperAdmin && user.roles && (
                                            <td>
                                                <p className={`user-roles-${user.id}`}>
                                                    {user.roles.join(', ')}
                                                </p>
                                            </td>
                                        )}
                                        <td className={`user-memberStatus-${user.id}`}>{user.memberStatus}</td>
                                        <td className={`user-associationRegistrationDate-${user.id}`}>{user.associationRegistrationDate ? new Date(user.associationRegistrationDate).toLocaleDateString() : '-'}</td>
                                        <td>
                                            <div>
                                                <input type="checkbox" id={`switch${user.id}`}
                                                       name={`switch-button-user-${user.id}`} defaultChecked={user.isAssociationMember}
                                                       data-switch="success"
                                                       data-url={`/api/admin/manager/user/${user.id}/change_status_is_association_member`}
                                                       className="switch_is_member_association"
                                                       onChange={() => this.handleSwitchChange(user.id, `/api/admin/manager/user/${user.id}/change_status_is_association_member`)}
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
                                            <button type="button" className="btn btn-outline-primary btn-sm" onClick={() => this.handleEditUser(user.id, user)}>
                                                <i className="mdi mdi-pencil-outline"></i>
                                            </button>
                                            {isSuperAdmin && (
                                                <button type="button" className="btn btn-danger btn-sm" onClick={() => this.handleDeleteUser(user.id)}>
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
                </div>
            </div>
        );
    }
}

export { ManageUser };
