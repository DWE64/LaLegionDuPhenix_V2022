import React from 'react';

class ManageUser extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            ...this.props.state
        };
    }
    render() {
        return (
            <div className="tab-content">
                <div className="tab-pane show active" id="basic-datatable-preview">
                    <table id="basic-datatable" className="table dt-responsive nowrap w-100">
                        <thead>
                        <tr>
                            <th>>Liste des utlisateurs</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            {% if is_granted('ROLE_SUPER_ADMIN') %}
                            <th>{{'admin.user.manage.role' | trans}}</th>
                            {% endif %}
                            <th>{{'admin.user.manage.member_status' | trans}}</th>
                            <th>{{'admin.user.manage.registration_date_in_association' | trans}}</th>
                            <th>{{'admin.user.manage.is_member' | trans}}</th>
                            <th>{{'admin.user.manage.created_at' | trans}}</th>
                            <th>{{'admin.user.manage.updated_at' | trans}}</th>
                            <th>{{'admin.user.manage.username' | trans}}</th>
                            <th>{{'admin.user.manage.birthday' | trans}}</th>
                            <th>{{'admin.user.manage.address' | trans}}</th>
                            <th>{{'admin.user.manage.seniority' | trans}}</th>
                            <th>{{'admin.user.manage.action' | trans}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for user in users %}
                        <tr className="user-row-{{ user.id }}">
                            <td>{{user.email}}</td>
                            <td className="user-name-{{ user.id }}">{{user.name}}</td>
                            <td className="user-firstname-{{ user.id }}">{{user.firstname}}</td>
                            {% if is_granted('ROLE_SUPER_ADMIN') %}
                            <td>
                                <p className="user-roles-{{ user.id }}">
                                    {% for role in user.roles %}
                                    {{role}},
                                    {% endfor %}
                                </p>
                                {% block buttonModalManageRoleUser %}
                                {{parent()}}
                                {% endblock %}
                            </td>
                            {% endif %}
                            <td className="user-memberStatus-{{ user.id }}">{{user.memberStatus}}</td>
                            <td className="user-associationRegistrationDate-{{ user.id }}">{{(user.associationRegistrationDate is not null) ? user.associationRegistrationDate|date("d/m/Y") : '-'}}</td>
                            <td>
                                <div>
                                    <input type="checkbox" id="switch{{ user.id }}"
                                           name="switch-button-user-{{ user.id }}" {{(user.isAssociationMember) ? 'checked' : ''}}
                                           data-switch="success"
                                           data-url="{{ path('app_admin_manage_user_change_status_member_association', {id: user.id}) }}"
                                           className="switch_is_member_association"
                                    />
                                    <label htmlFor="switch{{ user.id }}" data-on-label="Yes" data-off-label="No"
                                           className="mb-0 d-block"></label>
                                </div>
                            </td>
                            <td>{{user.createdAt | date("d/m/Y")}}</td>
                            <td className="user-updatedAt-{{ user.id }}">{{(user.updatedAt is not null) ? user.updatedAt|date("d/m/Y") : '-'}}</td>
                            <td className="user-username-{{ user.id }}">{{user.username}}</td>
                            <td className="user-birthday-{{ user.id }}">{{(user.birthday is not null) ? user.birthday|date("d/m/Y") : '-'}}</td>
                            <td className="user-address-{{ user.id }}">{{user.address}} - {{
                                user
                                .postalCode
                            }} {{user.city}}</td>
                            <td className="user-member-seniority-{{ user.id }}">
                                {{(user.memberSeniority is not null) ? user.memberSeniority : '-'}}
                            </td>
                            <td>
                                {% block buttonModalManageUser %}
                                {{parent()}}
                                {% endblock %}
                                {% if is_granted('ROLE_SUPER_ADMIN') %}
                                <form className="delete_user_form" method="post"
                                      action="{{ path('app_admin_delete_user', {id: user.id}) }}"
                                      style="display:inline;">
                                    <button type="submit" className="btn btn-danger btn-sm">
                                        <i className="mdi mdi-delete"></i>
                                    </button>
                                </form>
                                {% endif %}
                            </td>
                            {% block modalManageUser %}
                            {{parent()}}
                            {% endblock %}
                            {% block modalManageRoleUser %}
                            {{parent()}}
                            {% endblock %}
                        </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
                <!-- end preview-->
            </div>
        );
    }
}

export default AppReact;