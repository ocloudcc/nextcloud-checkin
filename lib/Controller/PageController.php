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
        $userId = $this->userSession->getUser()->getUID();
        $currentDate = (new \DateTime())->format('Y-m-d');

        $query = $this->db->prepare('SELECT * FROM `*PREFIX*user_checkins` WHERE `user_id` = ?');
        $query->execute([$userId]);
        $check_result = $query->fetch();

        // 随机获取0到200MB的额外空间,设置配额空间范围
        $rand_MB = rand(0, 200);
        
        function convertMbToGb($mb) {
        // 1 GB = 1024 MB
        $gb = $mb / 1024;
        // 保留2位小数
        return round($gb, 2) . ' GB';
        }
        
        $extraSpace =  convertMbToGb($rand_MB);
        
        function convertToGb($value) {
    // 去掉字符串中的 "GB" 并转换为浮点数
        return floatval(str_replace(" GB", "", $value));
        }

        function addQuotas($quota1, $quota2) {
    // 将配额转换为浮点数（GB）
        $gb1 = convertToGb($quota1);
        $gb2 = convertToGb($quota2);
    
    // 相加两个配额
        $totalGb = $gb1 + $gb2;
    
    // 格式化为 "xx GB" 格式，并保留两位小数
        return number_format($totalGb, 2) . " GB";
        }

        
        if ($check_result) {
            if ($check_result['checkin_date'] == $currentDate) {
            return new DataResponse(['message' => 'You have already checked in today.']);
            }

        $totalExtraSpace = addQuotas($check_result['total_extra_space'], $extraSpace);

        $updateQuery = $this->db->prepare('UPDATE `*PREFIX*user_checkins` SET `checkin_date` = ?, `extra_space` = ?, `total_extra_space` = ? WHERE `user_id` = ?');
        $updateQuery->execute([$currentDate, $extraSpace, $totalExtraSpace, $userId]);

            
        } else {
                // 将签到记录插入数据库
        $totalExtraSpace = $extraSpace;
        $insertQuery = $this->db->prepare('INSERT INTO `*PREFIX*user_checkins` (`user_id`, `checkin_date`, `extra_space`, `total_extra_space`) VALUES (?, ?, ?, ?)');
        $insertQuery->execute([$userId, $currentDate, $extraSpace, $totalExtraSpace]);
        }


        // 在此处处理为用户添加额外空间的逻辑

        $query = $this->db->prepare('SELECT `configvalue` FROM `*PREFIX*preferences` WHERE `userid` = ? AND `appid` = \'files\' AND `configkey` = \'quota\'');
        $query->execute([$userId]);
        $configValue = $query->fetchColumn();
        
        if ($configValue == 'default') {
           $query = $this->db->prepare('SELECT * FROM `*PREFIX*appconfig` WHERE `appid` = ? AND `configkey` = ?');
    
    // 执行查询并传递参数
            $query->execute(['files', 'default_quota']);
    
    // 获取结果
            $result = $query->fetch();

    // 检查并输出结果
            $defaultQuota = $result['configvalue'];
            $configValue = $defaultQuota;
        }
        if ($configValue !== 'none') {
        $addExtraSpace = addQuotas($configValue, $extraSpace);
        $updateQuery = $this->db->prepare('UPDATE `*PREFIX*preferences` SET `configValue` = ? WHERE `appid` = ? AND `configkey` = ?');
        $updateQuery->execute([$addExtraSpace, 'files', 'quota']);
        } else {
            return new DataResponse(['success' => true, 'message' => 'You are already an unlimited space user!', 'rand_MB' => $rand_MB,  'totalExtraSpace' => $totalExtraSpace]);
        }
        // 添加额外空间end

        return new DataResponse(['success' => true, 'message' => 'Check-in successful!', 'rand_MB' => $rand_MB,  'totalExtraSpace' => $totalExtraSpace]);
        } catch (\Exception $e) {
            return new DataResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
