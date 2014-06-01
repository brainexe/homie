<?php

namespace Raspberry\Scrabble;

use Matze\Core\Application\UserException;
use Matze\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class ScrabbleController extends AbstractController {

	/**
	 * @var ScrabbleGame
	 */
	private $_scrabble_game;

	/**
	 * @Inject("@ScrabbleGame")
	 */
	public function __construct(ScrabbleGame $scrabble_game) {
		$this->_scrabble_game = $scrabble_game;
	}

	/**
	 * @return string
	 * @Route("/scrabble/", name="scrabble.index")
	 */
	public function index(Request $request) {
		$games = $this->_scrabble_game->getGames();

		return $this->renderToResponse('scrabble/index.html.twig', [
			'games' => $games
		]);
	}

	/**
	 * @return string
	 * @Route("/scrabble/game/{game_id}/", name="scrabble.game.index")
	 */
	public function gameIndex(Request $requets, $game_id) {
		$user_points = $this->_scrabble_game->getPoints($game_id);
		$game_vo = $this->_scrabble_game->getGameVO($game_id);

		return $this->renderToResponse('scrabble/game.html.twig', [
			'user_names' => explode(',', $game_vo->user_names),
			'user_points' => $user_points,
			'game' => $game_vo
		]);
	}

	/**
	 * @throws UserException
	 * @return string
	 * @Route("/scrabble/start/", name="scrabble.start")
	 */
	public function startGame(Request $request) {
		$user_names = $request->request->get('user_names');
		$user_names = explode(',', $user_names);
		$user_names = array_map('trim', $user_names);

		if (empty($user_names)) {
			throw new UserException('No Users passed.');
		}

		$this->_scrabble_game->addGame($user_names);

		return new RedirectResponse('/scrabble/');
	}

	/**
	 * @return string
	 * @Route("/scrabble/add/", name="scrabble.add")
	 */
	public function addPoits(Request $request) {
		$game_id = $request->request->getInt('game_id');
		$points = $request->request->getInt('points');

		$this->_scrabble_game->addPoints($game_id, $points);

		return new RedirectResponse(sprintf('/scrabble/game/%d/', $game_id));
	}
} 