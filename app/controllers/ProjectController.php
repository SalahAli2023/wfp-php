<?php
namespace App\Controllers;

use App\Models\Project;
use App\Core\Validator;
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

//Project controller handling CRUD operations
class ProjectController {
    private Project $projectModel;
    private Request $request;
    private Response $response;
    private AuthMiddleware $auth;

    public function __construct() {
        $this->projectModel = new Project();
        $this->request = new Request();
        $this->response = new Response();
        $this->auth = new AuthMiddleware();
    }

    //Get all projects (public endpoint)
    public function index(): void {
        $limit = $_GET['limit'] ?? 10;
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $projects = $this->projectModel->getAll($limit, $offset);
        $total = $this->projectModel->getTotalCount();

        $this->response->success([
            'projects' => $projects,
            'pagination' => [
                'total' => $total,
                'page' => (int)$page,
                'limit' => (int)$limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    //Get single project by ID (public endpoint)
    public function show(int $id): void {
        $project = $this->projectModel->getById($id);
        
        if (!$project) {
            $this->response->notFound('Project not found');
            return;
        }

        $this->response->success(['project' => $project]);
    }

    //Create new project (protected endpoint)
    public function store(): void {
        // Check authentication
        if (!$this->auth->handle()) {
            return;
        }

        $data = $this->request->getJson();

        // Validate input
        $validator = new Validator($data);
        $validator->required(['title', 'description', 'target_amount'])
                 ->minLength('title', 5)
                 ->minLength('description', 10)
                 ->numeric('target_amount')
                 ->range('target_amount', 1, 1000000);

        if (!$validator->passes()) {
            $this->response->error('Validation failed', 422, $validator->getErrors());
            return;
        }

        // Get user from session
        $user = (new \App\Core\Session())->get('user');

        // Create project
        $projectId = $this->projectModel->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'target_amount' => $data['target_amount'],
            'created_by' => $user['id']
        ]);

        $this->response->success([
            'message' => 'Project created successfully',
            'project_id' => $projectId
        ], 'Project created', 201);
    }

    //Update project (protected endpoint)
    public function update(int $id): void {
        // Check authentication and admin role
        if (!$this->auth->handle() || !$this->auth->requireRole('admin')) {
            return;
        }

        $data = $this->request->getJson();

        // Validate input
        $validator = new Validator($data);
        $validator->minLength('title', 5)
                 ->minLength('description', 10)
                 ->numeric('target_amount')
                 ->range('target_amount', 1, 1000000);

        if (!$validator->passes()) {
            $this->response->error('Validation failed', 422, $validator->getErrors());
            return;
        }

        // Update project
        $success = $this->projectModel->update($id, $data);
        
        if (!$success) {
            $this->response->error('Failed to update project');
            return;
        }

        $this->response->success(['message' => 'Project updated successfully']);
    }

    //Delete project (protected endpoint)
    public function delete(int $id): void {
        // Check authentication and admin role
        if (!$this->auth->handle() || !$this->auth->requireRole('admin')) {
            return;
        }

        $success = $this->projectModel->delete($id);
        
        if (!$success) {
            $this->response->error('Failed to delete project');
            return;
        }

        $this->response->success(['message' => 'Project deleted successfully']);
    }
}