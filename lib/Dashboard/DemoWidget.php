<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2020, Georg Ehrke
 *
 * @author Georg Ehrke <oc.list@georgehrke.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\MopidyPlayer\Dashboard;

use OCA\MopidyPlayer\AppInfo\Application;
use OCP\Dashboard\IWidget;
use OCP\IInitialStateService;
use OCP\IL10N;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\UserStatus\IUserStatus;

/**
 * Class UserStatusWidget
 *
 * @package OCA\UserStatus
 */
class DemoWidget implements IWidget {

	/** @var IL10N */
	private $l10n;

	/** @var IUserManager */
	private $userManager;

	/** @var IUserSession */
	private $userSession;

	/**
	 * UserStatusWidget constructor
	 *
	 * @param IL10N $l10n
	 * @param IUserManager $userManager
	 * @param IUserSession $userSession
	 */
	public function __construct(IL10N $l10n,
								IUserManager $userManager,
								IUserSession $userSession) {
		$this->l10n = $l10n;
		$this->userManager = $userManager;
		$this->userSession = $userSession;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return Application::APP_ID;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('Demo Widget');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int {
		return 5;
	}

	/**
	 * @inheritDoc
	 */
	public function getIconClass(): string {
		return 'icon-user-status';
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): ?string {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function load(): void {
		\OCP\Util::addScript(Application::APP_ID, 'dashboard');

		/*$currentUser = $this->userSession->getUser();
		if ($currentUser === null) {
			$this->initialStateService->provideInitialState(Application::APP_ID, 'dashboard_data', []);
			return;
		}
		$currentUserId = $currentUser->getUID();

		// Fetch status updates and filter current user
		$recentStatusUpdates = array_slice(
			array_filter(
				$this->service->findAllRecentStatusChanges(8, 0),
				static function (UserStatus $status) use ($currentUserId): bool {
					return $status->getUserId() !== $currentUserId;
				}
			),
			0,
			7
		);

		$this->initialStateService->provideInitialState(Application::APP_ID, 'dashboard_data', array_map(function (UserStatus $status): array {
			$user = $this->userManager->get($status->getUserId());
			$displayName = $status->getUserId();
			if ($user !== null) {
				$displayName = $user->getDisplayName();
			}

			return [
				'userId' => $status->getUserId(),
				'displayName' => $displayName,
				'status' => $status->getStatus() === IUserStatus::INVISIBLE
					? IUserStatus::OFFLINE
					: $status->getStatus(),
				'icon' => $status->getCustomIcon(),
				'message' => $status->getCustomMessage(),
				'timestamp' => $status->getStatusTimestamp(),
			];
		}, $recentStatusUpdates));*/
	}
}
