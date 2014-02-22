function RouteCtrl($route) {

    var self = this;

    $route.when('/projects', {template:'tpl/project-list.html', controller:ProjectListCtrl});

    $route.when('/projects/:projectId', {template:'tpl/project-details.html', controller:ProjectDetailCtrl});

    $route.otherwise({redirectTo:'/projects'});

    $route.onChange(function () {
        self.params = $route.current.params;
    });

    $route.parent(this);

    this.addProject = function () {
        window.location = "#/projects/add";
    };

}

function ProjectListCtrl(Project) {

    this.projects = Wine.query();

}

function ProjectDetailCtrl(Project) {

    this.project = Project.get({wineId:this.params.wineId});


    this.saveProject = function () {
        if (this.project.id > 0)
            this.project.$update({projectId:this.project.id});
        else
            this.project.$save();
        window.location = "#/projects";
    }

    this.deleteProject = function () {
        this.project.$delete({projectId:this.project.id}, function() {
            alert('Project ' + project.name + ' deleted')
            window.location = "#/projects";
        });
    }

}