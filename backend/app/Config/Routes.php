<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('e_assessment/api/v1', function($routes){
	// Test endpoint for debugging
	$routes->get('test', function() {
		return service('response')->setJSON([
			'status' => 'success', 
			'message' => 'API is working',
			'timestamp' => date('Y-m-d H:i:s')
		]);
	});
	
	$routes->group('auth', function($routes){
		$routes->post('login', 'Auth::login');
		$routes->get('user', 'Auth::getCurrentUser');
		$routes->get('isAdmin', 'Auth::isAdmin');
	}); 

	$routes->group('users', function($routes){
		$routes->post('create', 'Users::registerUser');
		$routes->post('update', 'Users::updateUser');
		$routes->post('update/status', 'Users::updateUserStatus');
		$routes->post('changePassword', 'Users::ChangePassword');
		$routes->get('getUsersList', 'Users::getAllUserList');
		$routes->post('getUserById', 'Users::getUserDetails');
		
		// Admin User Management API
		$routes->post('admin/list', 'Users::getUsersForAdmin');
		$routes->post('admin/create', 'Users::createUserForAdmin');
		$routes->post('admin/update', 'Users::updateUserForAdmin');
		$routes->post('admin/delete', 'Users::deleteUserForAdmin');
		$routes->get('admin/types', 'Users::getUserTypes');
	});
	

	$routes->group('misc', function($routes){
		$routes->get('getUserTypes', 'Misc::getUserTypes');
		$routes->post('database/backup', 'BackupController::backupDatabase');
		$routes->post('database/restore', 'BackupController::restoreDatabase');

		// Category Routes
		$routes->get('categories', 'CategoryController::getCategories');
		$routes->post('categories/create', 'CategoryController::createCategory');
		$routes->post('categories/update', 'CategoryController::updateCategory');
		$routes->post('categories/delete', 'CategoryController::deleteCategory');

		// Market Value Routes
		$routes->get('market-values', 'MarketValueController::getMarketValues');
		$routes->post('market-values/create', 'MarketValueController::createMarketValue');
		$routes->post('market-values/update', 'MarketValueController::updateMarketValue');
		$routes->post('market-values/delete', 'MarketValueController::deleteMarketValue');

		// Assessment Level Routes
		$routes->get('assessment-levels', 'AssessmentLevelController::getAssessmentLevels');
		$routes->post('assessment-levels/create', 'AssessmentLevelController::createAssessmentLevel');
		$routes->post('assessment-levels/update', 'AssessmentLevelController::updateAssessmentLevel');
		$routes->post('assessment-levels/delete', 'AssessmentLevelController::deleteAssessmentLevel');

		// Assessment Requests Routes
		$routes->get('assessment-requests', 'AssessmentRequestController::getRequests');
		$routes->get('assessment-requests/(:num)', 'AssessmentRequestController::getRequest/$1');
		$routes->post('assessment-requests/create', 'AssessmentRequestController::createRequest');
		$routes->post('assessment-requests/update', 'AssessmentRequestController::updateRequest');
		$routes->post('assessment-requests/update-status', 'AssessmentRequestController::updateRequestStatus');
		$routes->post('assessment-requests/track', 'AssessmentRequestController::trackRequest');

		// Audit Log Routes
		$routes->get('audit-logs', 'AuditLogController::getLogs');
		$routes->get('audit-logs/(:num)', 'AuditLogController::getLog/$1');
		$routes->post('audit-logs/create', 'AuditLogController::createLog');
		$routes->get('audit-logs/statistics', 'AuditLogController::getStatistics');
		$routes->get('audit-logs/export', 'AuditLogController::exportLogs');
		$routes->post('audit-logs/cleanup', 'AuditLogController::cleanup');

		// Report Generation Routes
		$routes->post('reports/generate', 'ReportController::generateReport');
		$routes->get('reports/download/(:any)', 'ReportController::downloadReport/$1');
		$routes->get('reports/recent', 'ReportController::getRecentReports');
		$routes->post('reports/delete', 'ReportController::deleteReport');
	});

	$routes->group('dashboard', function($routes){
		$routes->get('statistics', 'DashboardController::getStatistics');
		$routes->get('municipalities', 'DashboardController::getMunicipalities');
		$routes->get('chart-data', 'DashboardController::getAssetsChartData');
		$routes->get('recent-activities', 'DashboardController::getRecentActivities');
	});

	$routes->group('transactions', function($routes){
		$routes->get('status/(:segment)', 'TransactionController::getRequestsByStatus/$1');
		$routes->get('quick-stats', 'TransactionController::getQuickStats');
		$routes->post('update-status', 'TransactionController::updateStatus');
		$routes->post('bulk-update', 'TransactionController::bulkUpdateStatus');
		$routes->get('test-approve/(:num)', 'TransactionController::testApprove/$1');
	});

	$routes->group('certificates', function($routes){
		$routes->get('(:num)', 'CertificateController::getAvailableCertificates/$1');
		$routes->get('ownership/(:num)', 'CertificateController::generateOwnershipCertificate/$1');
		$routes->get('tax-declaration/(:num)', 'CertificateController::generateTaxDeclarationCertificate/$1');
	});

	$routes->group('evaluations', function($routes){
		$routes->get('assigned', 'EvaluationController::getAssignedEvaluations');
		$routes->post('update', 'EvaluationController::updateEvaluation');
		$routes->get('certificates', 'EvaluationController::getEvaluatorCertificates');
		$routes->post('certificate/generate', 'EvaluationController::generateCertificate');
	});

	$routes->group('evaluator', function($routes){
		$routes->get('stats', 'EvaluationController::getEvaluatorStats');
		$routes->get('recent', 'EvaluationController::getRecentEvaluations');
	});

	$routes->group('property-values', function($routes){
		$routes->get('market-values', 'PropertyValueController::getMarketValues');
		$routes->get('assessment-levels', 'PropertyValueController::getAssessmentLevels');
		$routes->post('calculate', 'PropertyValueController::calculatePropertyValues');
		$routes->post('calculate-building', 'PropertyValueController::calculateBuildingValues');
		$routes->post('calculate-machinery', 'PropertyValueController::calculateMachineryValues');
	});
});