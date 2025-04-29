<?php

namespace App\Controllers;

use App\Models\PostModel;

class Post extends BaseController
{
    /** @var PostModel */
    protected $postModel;

    public function __construct()
    {
        // otomatis load helper jika perlu, misal 'form', 'url'
        helper(['form', 'url']);

        // instantiate model sekali
        $this->postModel = new PostModel();
    }

    /**
     * Tampilkan daftar post dengan pagination
     */
    public function index()
    {
        // ambil data paginate (2 item per halaman), grup 'posts'
        $posts  = $this->postModel->paginate(2, 'posts');
        $pager  = $this->postModel->pager;

        // susun data untuk view
        $data = [
            'title' => 'Daftar Post',
            'posts' => $posts,
            'pager' => $pager,
        ];

        // view di app/Views/posts/index.php
        return view('posts-index', $data);
    }

    /**
     * Tampilkan form untuk membuat post baru
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Post Baru',
            'validation' => \Config\Services::validation(),
        ];

        return view('create', $data);
    }

    /**
     * Simpan post hasil submit form
     */
    public function store()
    {
        // validasi input
        if (! $this->validate([
            'title'   => 'required|min_length[3]',
            'content' => 'required',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('validation', \Config\Services::validation());
        }

        // simpan ke database
        $this->postModel->insert([
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ]);

        return redirect()->to('/post')
            ->with('success', 'Post berhasil dibuat.');
    }

    /**
     * Tampilkan detail 1 post berdasarkan id
     */
    public function show($id = null)
    {
        $post = $this->postModel->find($id);

        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Post dengan ID {$id} tidak ditemukan.");
        }

        return view('posts/show', [
            'title' => $post['title'],
            'post'  => $post,
        ]);
    }

    /**
     * Tampilkan form edit post
     */
    public function edit($id = null)
    {
        $post = $this->postModel->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Post dengan ID {$id} tidak ditemukan.");
        }

        return view('posts/edit', [
            'title'      => 'Edit Post',
            'post'       => $post,
            'validation' => \Config\Services::validation(),
        ]);
    }

    /**
     * Proses update post
     */
    public function update($id = null)
    {
        if (! $this->validate([
            'title'   => 'required|min_length[3]',
            'content' => 'required',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('validation', \Config\Services::validation());
        }

        $this->postModel->update($id, [
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ]);

        return redirect()->to('/post')
            ->with('success', 'Post berhasil diubah.');
    }

    /**
     * Hapus post
     */
    public function delete($id = null)
    {
        $this->postModel->delete($id);
        return redirect()->to('/post')
            ->with('success', 'Post berhasil dihapus.');
    }
}
