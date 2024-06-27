import React from 'react';
import { UserApi } from '../api/userApi';

class ManageUser extends React.Component {
    constructor(props) {
        super(props);
        this.userApi = new UserApi();
        this.state = {
            users: this.props.state.users || [],
            isSuperAdmin: this.props.state.role === "ROLE_SUPER_ADMIN"
        };
    }

    render() {
        const { users, isSuperAdmin } = this.state;

        if (!users || users.length === 0) {
            return <div>Aucun utilisateur disponible</div>;
        }

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
                                    {isSuperAdmin && user.roles && (
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
                                            <form className="delete_user_form" method="post" action={`/api/admin/manage/user/${user.id}/delete`} style={{ display: 'inline' }}>
                                                <button type="submit" className="btn btn-danger btn-sm">
                                                    <i className="mdi mdi-delete"></i>
                                                </button>
                                            </form>
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

export { ManageUser };
