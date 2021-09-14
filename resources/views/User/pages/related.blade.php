<section id="ez-body__right-sidebar" class="col-lg-3 ez-sidebar">
    <ul class="pt-4" id="right-sidebar__items">
        @if(count($related_pages) > 0)
            <li class="mt-3">
                <h6 class="pb-2" style="font-weight: bold;font-size: 15px">Pages You May Like</h6>
                <div class="suggested-groups">
                    @foreach($related_pages as $page)
                    <div class="card">
                      <a href="/pages/{{$page->id}}">
                          <img src="{{asset('media')}}/{{$page->profile_image}}" class="card-img-top" alt="...">
                      </a>
                      <div class="d-flex justify-content-between">
                          <div class="card-body">
                              <a href="pages/{{$page->id}}" style="color:black !important">
                                  <h3 class="card-title">{{$page->name}}</h3>
                              </a>
                              <p class="card-text"><small class="text-muted" id="{{$page->id}}">
                                  <?php
                                      $member = App\models\PageMember::where('page_id',$page->id)->where('state',1)->where('isAdmin','!=', 1)->count();
                                      echo $member;
                                  ?>
                                  </small>
                                  {{__('pages.member')}}
                              </p>
                          </div>
                          <div class="p-2">
                                @if(Auth::guard('web')->user())
                                  <?php
                                      $checkState = App\models\PageMember::where('page_id',$page->id)->where('user_id',auth::user()->id)->get();
                                  ?>
                                  @if (count($checkState)==0)
                                  <div class="p-2">
                                          <button class="button-4 totyPage" id="join|{{$page->id}}" >{{__('pages.like')}} </button>
                                  </div>

                                  @elseif (count($checkState)>0)
                                      @if ($checkState[0]->state == 1 && $checkState[0]->isAdmin != 1)
                                          <div class="p-2">
                                                  <button class="button-2 totyPage" id="leave|{{$page->id}}">{{__('pages.dislike')}}</button>
                                          </div>

                                      @elseif ($checkState[0]->state == 2)
                                          <div class="p-2">
                                              <button class="button-2 totyPage" id="leave|{{$page->id}}">{{__('pages.dislike_request')}}</button>
                                          </div>

                                      @elseif ($checkState[0]->isAdmin == 1)
                                          <div class="p-2">
                                              <button class="button-2">{{__('pages.admin')}}</button>
                                          </div>
                                      @endif
                                  @endif
                                @else
                                    <form action="/login" method="post">
                                        @csrf
                                        <button class="button-4">{{__('pages.like')}}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>
</section>
