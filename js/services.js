angular.service('Project', function ($resource) {
    return $resource('api/projects/:projectId', {}, {
        update: {method:'PUT'}
    });
});
