<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>Challenge #2: 3-10 May 2022</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/vendor/bs5/css/bootstrap.min.css">
    <!--    Datatable-->
    <link rel="stylesheet" type="text/css" href="/vendor/datatable/datatables.min.css"/>
    <!-- Main Style -->
    <link href="/css/main.css" rel="stylesheet" type="text/css">
    <link href="/css/table.css" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    <script type="text/javascript" src="/vendor/bs5/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/vendor/jquery351/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="/vendor/datatable/datatables.min.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
</head>
<body>
<form method="post" enctype="multipart/form-data" action="">
    <div class="container pt-3">
        <div class="alert alert-primary" role="alert">
            <strong>Challenge #2:</strong> 3-10 May 2022
        </div>
        <?php

        if ($_POST != null) {

//            var_dump($_POST);
            $post = $_POST;

            if ($post['user'] != '') {
                $url = 'https://api.github.com/users/';
                $searchUserURL = 'https://api.github.com/search/users?q=';
                //repos?per_page=69&page=1 - რომ არ დამავიწყდეს
                $perpage = 'per_page=';
                $page = 'page=1';
                $repo = '/repos';
                $fol = '/followers';
                $user = $post['user'];
                $param = [
                    'http' => [
                        'method' => 'GET',
                        'header' => [
                            'User-Agent: PHP'
                        ]
                    ]
                ];
                //user -ის არსებობის შემოწმება
                $json = file_get_contents($searchUserURL . $user, false, stream_context_create($param));
                $data = json_decode($json, true);
                if ($data['total_count'] != 0) {
                    //არსებული user -ის მონაცემების წამოღება
                    $json = file_get_contents($url . $user, false, stream_context_create($param));
                    $data = json_decode($json, true);
                    $nRepos = $data['public_repos']; //რეპოზიტორების რაოდენობა
                    $nFol = $data['followers']; //ფოლლოუერების რაოდენობა
                    $avatarURL = $data['avatar_url'];

                    //რეპოზიტორების და ფოლოუერების წამოღება
                    $n = 10;
                    $json = file_get_contents($url . $user . $repo . '?' . $perpage . $nRepos . '&' . $page, false, stream_context_create($param));
                    $dataREPO = json_decode($json, true);
                    $json = file_get_contents($url . $user . $fol . '?' . $perpage . $nFol . '&' . $page, false, stream_context_create($param));
                    $dataFOL = json_decode($json, true);

                    form($avatarURL, $user, $nRepos, $nFol);
                } else {
                    form('/img/nonepawn.jpg', $user, 0, 0);
                }

            } else {
                form('/img/nonepawn.jpg', '', 0, 0);
            }
        } else {
            form('/img/pawn.jpg', '', 0, 0);
        }


        function form($a, $b, $c, $d)
        {
            echo '<div class="col d-flex justify-content-center">
    <div class="card mb-3 w-50 border-0" style="max-width: 600px;">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="' . $a . '" class="img-fluid img-thumbnail rounded-circle" alt="user" style="width: 200px">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <div class="mb-1">
                        <input type="text" class="form-control" id="user" name="user"
                               placeholder="Fill user name" value="' . $b . '">
                    </div>
                    <div class="mb-1">
                        <div class="input-group">
                            <input type="text" class="form-control" id="repos" name="repos" value="Repositories"
                                   readonly>
                            <span class="input-group-text">' . $c . '</span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="input-group">
                            <input type="text" class="form-control" id="followers" name="followers"
                                   value="Followers" readonly>
                            <span class="input-group-text">' . $d . '</span>
                        </div>
                    </div>
                    <button type="submit" name="preview" value="" class="btn btn-primary w-100">GET INFO
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>';
        }

        ?>


        <div class="row row-cols-1">
            <div class="col">
                <div class="card">
                    <div class="card-body">

                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="Repositories-tab" data-bs-toggle="pill"
                                        data-bs-target="#Repositories" type="button" role="tab"
                                        aria-selected="true">REPOSITORIES
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="Followers-tab" data-bs-toggle="pill"
                                        data-bs-target="#Followers" type="button" role="tab"
                                        aria-selected="false">FOLLOWERS
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="Repositories" role="tabpanel"
                                 tabindex="0">
                                <table id="rtable"
                                       class="display table-responsive-sm table-sm table-striped table-bordered hover dataTable w-100">
                                    <thead class="">
                                    <tr>
                                        <th>Name</th>
                                        <th>html_url</th>
                                        <th>description</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($dataREPO) {
                                        foreach ($dataREPO as $key => $value) {
                                            echo '<tr>';
                                            echo '<td>' . $value['name'] . '</td>';
                                            echo '<td><a href="' . $value['html_url'] . '">' . $value['html_url'] . '</a></td>';
                                            echo '<td>' . $value['description'] . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="Followers" role="tabpanel"
                                 tabindex="0">
                                <table id="ftable"
                                       class="display table-sm table-striped table-bordered hover dataTable w-100">
                                    <thead class="">
                                    <tr>
                                        <th>Login</th>
                                        <th>html_url</th>
                                        <th>Avatar</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($dataFOL) {
                                        foreach ($dataFOL as $key => $value) {
                                            echo '<tr>';
                                            echo '<td>' . $value['login'] . '</td>';
                                            echo '<td><a href="' . $value['html_url'] . '">' . $value['html_url'] . '</a></td>';
                                            echo '<td><img src="' . $value['avatar_url'] . '" alt="' . $value['avatar_url'] . '" width="50"></td>';
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</form>
<script>
    $(document).ready(function () {
        $('table').DataTable();
    });
</script>
</body>
</html>