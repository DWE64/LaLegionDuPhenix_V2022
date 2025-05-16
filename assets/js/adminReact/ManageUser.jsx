import React from 'react';
import { UserApi } from '../api/userApi';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import Table from 'react-bootstrap/Table';
import {Col, Container, InputGroup, Row, Form, Button, FormGroup, Badge} from "react-bootstrap";
import DatePicker from "react-datepicker";

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
            },
            editingUserId: null,
            editedUser: {},
            searchQuery: ''
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

    handleEditUser = (userId, user) => {
        this.setState({
            editingUserId: userId,
            editedUser: { ...user } // Créer une copie de l'utilisateur pour l'édition
        });
    };

    handleSaveUser  = () => {
        const { editingUserId, editedUser } = this.state;
        const url = `/api/admin/manager/user/${editingUserId}/edit`;

        this.userApi.updateUser(url, editedUser)
            .then((response) => {
                const updatedUser = response.data;

                this.setState((prevState) => {
                    const updatedUsers = prevState.users.map((user) => {
                        if (user.id === editingUserId) {
                            return {
                                ...user,
                                ...updatedUser
                            };
                        }
                        return user;
                    });

                    return {
                        users: updatedUsers,
                        editingUserId: null,  // Réinitialiser l'identifiant de l'utilisateur en édition
                        editedUser: {}        // Réinitialiser les données de l'utilisateur édité
                    };
                });
            })
            .catch((error) => {
                console.error('Échec de la mise à jour:', error);
            });
    };

    handleInputChange = (e, field) => {
        const { editedUser } = this.state;
        this.setState({
            editedUser: {
                ...editedUser,
                [field]: e.target.value
            }
        });
    };

    handleSort = (key) => {
        this.setState((prevState) => {
            let direction = 'asc';
            if (prevState.sortConfig.key === key && prevState.sortConfig.direction === 'asc') {
                direction = 'desc';
            }

            return {
                sortConfig: { key, direction }
            };
        });
    };

    handleRolesChange = (e) => {
        const options = e.target.options;
        const selectedRoles = [];

        for (let i = 0; i < options.length; i++) {
            if (options[i].selected) {
                selectedRoles.push(options[i].value);
            }
        }

        this.setState((prevState) => ({
            editedUser: {
                ...prevState.editedUser,
                roles: selectedRoles
            }
        }));
    };

    handleSearchChange = (e) => {
        const searchQuery = e.target.value.toLowerCase();
        this.setState({ searchQuery });
    };

    getFilteredUsers = () => {
        const { users, searchQuery, sortConfig } = this.state;

        let filteredUsers = users;

        if (searchQuery) {
            const query = searchQuery.toLowerCase();
            filteredUsers = users.filter(user => {
                // On transforme chaque champ en chaîne non nulle
                const name     = (user.name     || '').toLowerCase();
                const email    = (user.email    || '').toLowerCase();
                const username = (user.username || '').toLowerCase();
                return name.includes(query)
                    || email.includes(query)
                    || username.includes(query);
            });
        }

        if (sortConfig) {
            filteredUsers = filteredUsers.sort((a, b) => {
                if (a[sortConfig.key] < b[sortConfig.key]) {
                    return sortConfig.direction === 'asc' ? -1 : 1;
                }
                if (a[sortConfig.key] > b[sortConfig.key]) {
                    return sortConfig.direction === 'asc' ? 1 : -1;
                }
                return 0;
            });
        }

        return filteredUsers;
    };

    handleInputChangeDate = (value, field) => {
        this.setState((prevState) => ({
            editedUser: {
                ...prevState.editedUser,
                [field]: value
            }
        }));
    };

    render() {
        const { users, isSuperAdmin, sortConfig, editedUser, editingUserId } = this.state;

        if (!users || users.length === 0) {
            return <div>Aucun utilisateur disponible</div>;
        }

        return (
            <Container fluid={true}>
                <Row className="justify-content-end">
                    <Col className="col-md-3 col-12">
                        <InputGroup>
                            <Form.Control
                                placeholder="Rechercher un utilisateur..."
                                value={this.state.searchQuery}
                                onChange={this.handleSearchChange}
                                className="search-input"
                            />
                        </InputGroup>
                    </Col>
                </Row>
                <Row>
                    <Col>
                        <Table size="sm" responsive={true}>
                            <thead>
                            <tr>
                                <th onClick={() => this.handleSort('email')}>Email {sortConfig.key === 'email' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('name')}>Nom {sortConfig.key === 'name' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('firstname')}>Prénom {sortConfig.key === 'firstname' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                {isSuperAdmin &&
                                    <th onClick={() => this.handleSort('roles')}>Rôle {sortConfig.key === 'roles' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>}
                                <th onClick={() => this.handleSort('associationRegistrationDate')}>Date
                                    d'inscription {sortConfig.key === 'associationRegistrationDate' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('isAssociationMember')}>Membre {sortConfig.key === 'isAssociationMember' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('createdAt')}>Date de
                                    création {sortConfig.key === 'createdAt' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('updatedAt')}>Date de mise à
                                    jour {sortConfig.key === 'updatedAt' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('username')}>Nom
                                    d'utilisateur {sortConfig.key === 'username' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('birthday')}>Date de
                                    naissance {sortConfig.key === 'birthday' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('address')}>Adresse {sortConfig.key === 'address' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('postalCode')}>CP {sortConfig.key === 'postalCode' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('city')}>Ville {sortConfig.key === 'city' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th onClick={() => this.handleSort('memberSeniority')}>Ancienneté {sortConfig.key === 'memberSeniority' ? (sortConfig.direction === 'asc' ? '▲' : '▼') : ''}</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            {this.getFilteredUsers().map((user) => (
                                <tr key={user.id} className={`user-row-${user.id}`}>
                                    <td>{editingUserId === user.id ? (
                                        <InputGroup>
                                            <Form.Control
                                                value={editedUser.email}
                                                onChange={(e) => this.handleInputChange(e, 'email')}
                                            />
                                        </InputGroup>
                                    ) : (user.email)} {user.memberStatus !== 'MEMBER_REGISTER' ? (<Badge bg="danger">NON-MEMBRE</Badge>): (<Badge bg="success">MEMBRE</Badge>)} </td>
                                    <td>{editingUserId === user.id ? (
                                            <InputGroup>
                                                <Form.Control
                                                    value={editedUser.name}
                                                    onChange={(e) => this.handleInputChange(e, 'name')}
                                                />
                                            </InputGroup>
                                    ) : (user.name)}</td>
                                    <td>{editingUserId === user.id ? (
                                        <InputGroup>
                                            <Form.Control
                                                value={editedUser.firstname}
                                                onChange={(e) => this.handleInputChange(e, 'firstname')}
                                            />
                                        </InputGroup>
                                    ) : (user.firstname)}</td>
                                    {isSuperAdmin && user.roles && (
                                        <td>
                                            {editingUserId === user.id ? (
                                                <Form.Select
                                                    size="sm"
                                                    value={editedUser.roles}
                                                    onChange={(e) => this.handleRolesChange(e)}
                                                    >
                                                    {this.props.state.listRole.map((role) => (
                                                        <option key={role} value={role}>
                                                            {role}
                                                        </option>
                                                    ))}
                                                </Form.Select>
                                            ) : (
                                                <p className={`user-roles-${user.id}`}>
                                                    {user.roles.join(', ')}
                                                </p>
                                            )}
                                        </td>
                                    )}
                                    <td>{user.associationRegistrationDate && user.associationRegistrationDate !== '-' ? new Date(user.associationRegistrationDate).toLocaleDateString() : '-'}</td>
                                    <td>
                                        <div>
                                            <input
                                                type="checkbox"
                                                id={`switch${user.id}`}
                                                name={`switch-button-user-${user.id}`}
                                                defaultChecked={user.isAssociationMember}
                                                data-switch="success"
                                                data-url={`/api/admin/manager/user/${user.id}/change_status_is_association_member`}
                                                className="switch_is_member_association"
                                                onChange={() => this.handleSwitchChange(user.id, `/api/admin/manager/user/${user.id}/change_status_is_association_member`)}
                                            />
                                            <label htmlFor={`switch${user.id}`} data-on-label="Yes"
                                                   data-off-label="No" className="mb-0 d-block"></label>
                                        </div>
                                    </td>
                                    <td>{user.createdAt && user.createdAt !== '-' ? new Date(user.createdAt).toLocaleDateString() : '-'}</td>
                                    <td>{user.updatedAt && user.updatedAt !== '-' ? new Date(user.updatedAt).toLocaleDateString() : '-'}</td>
                                    <td>{editingUserId === user.id ? (
                                        <InputGroup>
                                            <Form.Control
                                                value={editedUser.username}
                                                onChange={(e) => this.handleInputChange(e, 'username')}
                                            />
                                        </InputGroup>
                                    ) : (
                                        user.username
                                    )}</td>
                                    <td>
                                        {editingUserId === user.id ? (
                                            <DatePicker
                                                selected={new Date()}
                                                onChange={(e) => this.handleInputChangeDate(e, 'birthday')}
                                                dateFormat="dd/MM/yyyy"
                                            />
                                        ) : (
                                            user.birthday ? new Date(user.birthday).toLocaleDateString() : '-'
                                        )}
                                    </td>
                                    <td>
                                        {editingUserId === user.id ? (
                                            <InputGroup>
                                                <Form.Control
                                                    value={editedUser.address}
                                                    onChange={(e) => this.handleInputChange(e, 'address')}
                                                />
                                            </InputGroup>
                                        ) : (
                                            user.address
                                        )}
                                    </td>
                                    <td>
                                        {editingUserId === user.id ? (
                                            <InputGroup>
                                                <Form.Control
                                                    value={editedUser.postalCode}
                                                    onChange={(e) => this.handleInputChange(e, 'postalCode')}
                                                />
                                            </InputGroup>
                                        ) : (
                                            user.postalCode
                                        )}
                                    </td>
                                    <td>
                                        {editingUserId === user.id ? (
                                            <InputGroup>
                                                <Form.Control
                                                    value={editedUser.city}
                                                    onChange={(e) => this.handleInputChange(e, 'city')}
                                                />
                                            </InputGroup>
                                        ) : (
                                            user.city
                                        )}
                                    </td>
                                    <td>
                                        {editingUserId === user.id ? (
                                            <Form.Select
                                                size="sm"
                                                value={editedUser.memberSeniority}
                                                onChange={(e) => this.handleInputChange(e, 'memberSeniority')}
                                            >
                                                {this.props.state.listSeniority.map((oldest) => (
                                                    <option key={oldest} value={oldest}>
                                                        {oldest}
                                                    </option>
                                                ))}
                                            </Form.Select>
                                        ) : (
                                            user.memberSeniority !== '' ? (
                                                user.memberSeniority === 'MEMBER_NEW' ? (
                                                    <Badge bg="info">{user.memberSeniority}</Badge>
                                                ) : (
                                                    <Badge bg="warning">{user.memberSeniority}</Badge>
                                                )
                                            ) : (
                                                '-'
                                            )
                                        )}
                                    </td>
                                    <td className="px-0">
                                        {editingUserId === user.id ? (
                                            <Button variant="success" size="sm" className="px-1 py-0 mr-1"
                                                    onClick={this.handleSaveUser}>
                                                <i className="mdi mdi-content-save"></i>
                                            </Button>
                                        ) : (
                                            <Button variant="primary" size="sm" className="px-1 py-0 mr-1"
                                                    onClick={() => this.handleEditUser(user.id, user)}>
                                                <i className="mdi mdi-pencil-outline"></i>
                                            </Button>
                                        )}
                                        {isSuperAdmin && (
                                            <Button variant="danger" size="sm" className="px-1 py-0 ms-1"
                                                    onClick={() => this.handleDeleteUser(user.id)}>
                                                <i className="mdi mdi-delete"></i>
                                            </Button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </Table>
                    </Col>
                </Row>
            </Container>
        );
    }
}

export {ManageUser};
