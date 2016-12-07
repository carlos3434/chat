<?php
use LaravelRealtimeChat\Repositories\Area\AreaRepository;
use LaravelRealtimeChat\Repositories\User\UserRepository;

class AreaController extends \BaseController {
    /**
     * @var Chat\Repositories\AreaRepository
     */
    private $areaRepository; 
    /**
      * @var Chat\Repositories\UserRepository
     */
    private $userRepository;
    public function __construct( 
        AreaRepository $areaRepository,
        UserRepository $userRepository
    ){
        $this->areaRepository = $areaRepository;
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of user conversations.
     *
     * @return Response
     */
    public function index() {
        $areas = $this->areaRepository->getAllActives();
        /*foreach($areas as $key => $area) {
            $viewData['areas'][$area->id] = $area->nombre;
        }*/
        $response=[
            'areas' => $areas,
        ];
        return Response::json($response);
    }
    /**
     * Display a listing of user conversations.
     *
     * @return Response
     */
    public function show($area_id){
        $usuarios = $this->userRepository->getAllExceptFromArea(Auth::user()->id,$area_id);
        $response=['users'=>$usuarios];
        return Response::json($response);
        //return $usuarios->lists('full_name', 'id');
    }
}
