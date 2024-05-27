<?php
namespace OCA\Checkin\Controller;

use OCP\IRequest;
use OCP\IUserSession;
use OCP\IDBConnection;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;

class PageController extends Controller {
    private $userSession;
    private $db;

    public function __construct($AppName, IRequest $request, IUserSession $userSession, IDBConnection $db) {
        parent::__construct($AppName, $request);
        $this->userSession = $userSession;
        $this->db = $db;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        return new TemplateResponse('checkin', 'main');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function checkin() {
        try {
            $user = $this->userSession->getUser();
            if ($user === null) {
                throw new \Exception('User not logged in');
            }

            $uid = $user->getUID();


            $query = $this->db->prepare('SELECT checkin_count FROM oc_user_checkins WHERE uid = ?');
            $query->execute([$uid]);
            $row = $query->fetch();

            if ($row) {

                $newCount = $row['checkin_count'] + 1;
                $updateQuery = $this->db->prepare('UPDATE oc_user_checkins SET checkin_count = ? WHERE uid = ?');
                $updateQuery->execute([$newCount, $uid]);
            } else {

                $insertQuery = $this->db->prepare('INSERT INTO oc_user_checkins (uid, checkin_count) VALUES (?, ?)');
                $insertQuery->execute([$uid, 1]);
            }

            return new DataResponse(['success' => true]);
        } catch (\Exception $e) {
            return new DataResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
