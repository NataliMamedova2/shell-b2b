# View

- Action of controller must return ``CrudBundle\Interfaces\Response`` interface

Each `block` contain:

```yaml
admin_show_list:                ## route name (required)
    extend: 'admin_base_list'   ## parent block
    view_model: 'crud.view_view_model.list_view_model'      ## ModelInterface service
    template: '/backend/crud/content/list.html.twig'        ## twig template (required if extend block dont have template)
    data: [#array#]
    children: [#children blocks array#]
```

if exists key `extend` - other key not required and will be inherited from parent view. Each key will be
merged with parent. You can describe some blocks one time and reuse it anywhere.

#### Block options

- `extend` - describe parent view, all config key will be inherited and merged with current config
- `template` - path to template for current block
- `view_model` - specific `ViewModel` (``CrudBundle\View\ModelInterface``) service for current block 
(it can hide complex view logic for current block) ``CrudBundle\View\ModelInterface``
- `options` - specific options which `view_model` must have
- `data` - array for variables in view. `data` can be `data['static']`, `data['global']` and `data['parent']`. 
  - `data['static']` - static variables, for example labels, icons, some text etc.
  - `data['global']` - data, which controller returned.
  - `data['parent']` - data from parent block
- `children` - describe child blocks, which describe as block and will be accessed like property in template:
- `flash_message` - `FlashBagInterface` message data: `type` and `message`. Will show message after `RedirectResponse`.

## Example config

```yaml
crud:
    view:
        contents:
            ## Simple list
            admin_users_user_list:          
                template: '/backend/crud/content/list.html.twig'        ## twig template
                data:
                    static:
                        title: 'header.users_list'
                        icon: 'users'
                    global: 'result'        ## array data from controller. variables from toArray method of CrudBundle\Interfaces\Response
                children:
                    button-new-entity:      ## link button block. (render button with link to 'routeName')
                        template: '/backend/crud/block/link-button.html.twig'
                        data:
                            static:
                                title: 'btn.new_entity'
                                color: 'success'
                                icon: 'plus'
                                routeName: 'admin_users_user_new'
                    table:                  ## table block
                        template: '/backend/users/table.html.twig'
                        data:
                            parent:
                                result: 'paginator'     ## now you can get 'paginator' variable in '/backend/users/table.html.twig'
                    paginator:              ## paginator block
                        template: '/backend/crud/block/paginator.html.twig'
                        data:
                            parent:
                                result: 'paginator'
            
            ## form create
            admin_users_user_new:
                template: '/backend/crud/content/read.html.twig'
                data:
                    static:
                        title: 'header.users_create'
                        icon: 'user'
                children:
                    form:
                        view_model: 'crud.view_view_model.form_view_model'
                        template: '/backend/crud/block/form.html.twig'
                        options:
                            form_type: App\Users\View\Form\UserFormType
                            form_options:
                                method: 'post'
                        data:
                            static:
                                actionRoute: 'admin_users_user_create'      ## action route form submit
                            global:
                                data: data
                                result: result
                                errors: errors
                        children:
                            actions:
                                children:
                                    button-save:
                                        template: '/backend/crud/block/submit-button.html.twig'
                                        data:
                                            static:
                                                title: 'btn.save_and_edit'
                                                color: 'success'
                                                icon: 'save'
                                    button-save-redirect:
                                        template: '/backend/crud/block/submit-redirect-button.html.twig'
                                        data:
                                            static:
                                                title: 'btn.save_and_back'
                                                color: 'info'
                                                icon: 'save'
                                                redirect: 'admin_users_user_list'
                                    button-cancel:
                                        template: '/backend/crud/block/link-button.html.twig'
                                        data:
                                            static:
                                                title: 'btn.back'
                                                color: 'default'
                                                icon: 'list'
                                                routeName: 'admin_users_user_list'
            ##
            admin_users_user_create:
                extend: 'admin_users_user_new'
            ##
            admin_users_user_read:
                extend: 'admin_users_user_new'
                data:
                    static:
                        title: 'header.users_update'
                children:
                    form:
                        data:
                            static:
                                actionRoute: 'admin_users_user_update'
            ##
            admin_users_user_update:
                extend: 'admin_users_user_read'
            admin_users_user_delete:
                flash_message:
                    type: 'success'
                    message: 'message.user_deleted.success'
```

# Routes Crud

- list
    - controller: 'crud.action.list_action'
    - requirements: 
        - request: CrudBundle\Interfaces\ListQueryRequest - ``not required``
        - paginator: Infrastructure\Interfaces\Paginator\Paginator - ``required``
- new
    - controller: 'crud.action.empty_action'  ## empty controller
- read
    - controller: 'crud.action.read_action'
    - requirements: 
        - request: CrudBundle\Interfaces\ReadQueryRequest - ``not required``
        - repository: Infrastructure\Interfaces\Repository\Repository - ``required``
- update
    - controller: 'crud.action.command_action'
    - requirements: 
        - request: Domain\Interfaces\HandlerRequest - ``required``
        - handler: Domain\Interfaces\Handler - ``required``
        - redirectTo: 'admin_users_user_read' - ``required`` ## on which route redirect after success submit data
- create
    - controller: 'crud.action.command_action'
    - requirements: 
        - request: Domain\Interfaces\HandlerRequest - ``required``
        - handler: Domain\Interfaces\Handler - ``required``
        - redirectTo: 'admin_users_user_read' - ``required`` ## on which route redirect after success submit data
- delete
    - controller: 'crud.action.command_action'
    - requirements: 
        - request: Domain\Interfaces\HandlerRequest - ``required``
        - handler: Domain\Interfaces\Handler - ``required``
        - redirectTo: 'admin_users_user_read' - ``required`` ## on which route redirect after success submit data

## Crud route sample
```yaml
admin_users_user_list:
    path: /admin/users/user/list
    methods: [GET]
    controller: 'crud.action.list_action'
    requirements:
        request: 'app.users.action.backend.list_action.query_request'
        paginator: 'app.users.infrastructure.user.paginator'

admin_users_user_new:
    path: /admin/users/user/new
    methods: [GET]
    controller: 'crud.action.empty_action'

admin_users_user_read:
    path: /admin/users/user/read/{id}
    methods: [GET]
    controller: 'crud.action.read_action'
    requirements:
        id: "%routing.uuid%"
        repository: "app.users.infrastructure.user.repository"

admin_users_user_update:
    path: /admin/users/user/update/{id}
    methods: [POST]
    controller: 'crud.action.command_action'
    requirements:
        id: "%routing.uuid%"
        request: 'App\Users\Domain\User\UseCase\Update\HandlerRequest'
        handler: 'app.users.domain.update.handler'
        redirectTo: 'admin_users_user_read'

admin_users_user_create:
    path: /admin/users/user/create
    methods: [POST]
    controller: 'crud.action.command_action'
    requirements:
        request: 'App\Users\Domain\User\UseCase\Create\HandlerRequest'
        handler: 'app.users.domain.create.handler'
        redirectTo: 'admin_users_user_read'

admin_users_user_delete:
    path: /admin/users/user/delete/{id}
    methods: [POST]
    controller: 'crud.action.command_action'
    requirements:
        request: 'App\Users\Domain\User\UseCase\Delete\HandlerRequest'
        handler: 'app.users.domain.delete.handler'
        redirectTo: 'admin_users_user_list'
```
