<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\provinsi;
use App\Models\solutions_package;
use App\Models\SubSolutionPackage;
use Illuminate\Http\Request;

class SubSolutionPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $param = ['data' => SubSolutionPackage::paginate(10)];
        return view('dashboard.sub_solution_package.sub_solution_package', $param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $param = ['barang' => product::all(), 'sp' => solutions_package::all()];
        return view('dashboard.sub_solution_package.form_sub_solution_package', $param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':attribute wajib diisi',
            'max' => ':attribute maksimal :max karakter',
        ];
        $request->validate([
            'solution_package' => 'required',
            'nama_barang' => 'required',
            'jumlah' => 'required|max:255',

        ], $messages);
        try {


            $prod = new ProductController;
            $modelProd = new product();
            $modelProd->sku = $request->nama_barang;
            $namaBarang = $prod->show($modelProd)->nama;

            $solutionPackage = new SolutionsPackageController;
            $modelSolutionPackage = new solutions_package();
            $modelSolutionPackage->id = $request->solution_package;
            $resp = $solutionPackage->show($modelSolutionPackage);

            if (SubSolutionPackage::where('sku', $request->nama_barang)->where('id_solution_package',$resp->id)->exists()) {
                throw new \Exception("Package With Product Name " . $namaBarang . " Already Exist With Solution Package ".$resp->nama_solution."-".$resp->nama_object);

            }

           
            $object = $resp->nama_object;
            $solution = $resp->nama_solution;
            $tb = new SubSolutionPackage();
            $tb->sku = $request->nama_barang;

            $tb->id_solution_package = $request->solution_package;
            $tb->package = $solution . '-' . $object;
            $tb->nama_barang = $namaBarang;
            $tb->jumlah = $request->jumlah;
            $tb->ruangan = $request->has('ruangan') ? true : false;
            $tb->lantai = $request->has('lantai') ? true : false;
            $tb->save();

        } catch (\Throwable $th) {
            return redirect()->back()->withErrors([$th->getMessage()]);
        }

        return redirect('/sub_solution_package')->with('success', 'Berhasil menambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\SubSolutionPackage $subSolutionPackage
     * @return \Illuminate\Http\Response
     */
    public function show(SubSolutionPackage $subSolutionPackage)
    {
        return SubSolutionPackage::find($subSolutionPackage->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\SubSolutionPackage $subSolutionPackage
     * @return \Illuminate\Http\Response
     */
    public function edit(SubSolutionPackage $subSolutionPackage)
    {
        $param = ['old' => $this->show($subSolutionPackage), 'barang' => product::all(), 'sp' => solutions_package::all()];
        return view('dashboard.sub_solution_package.form_sub_solution_package', $param);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SubSolutionPackage $subSolutionPackage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubSolutionPackage $subSolutionPackage)
    {
        $messages = [
            'required' => ':attribute wajib diisi',
            'max' => ':attribute maksimal :max karakter',

        ];
        $request->validate([
            'jumlah' => 'required|max:255',

        ], $messages);

        try {
            $subSolutionPackage->jumlah = $request->jumlah;
            $subSolutionPackage->ruangan = $request->has('ruangan') ? true : false;
            $subSolutionPackage->lantai = $request->has('lantai') ? true : false;
            $subSolutionPackage->save();
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error', $th->getMessage()]);
        }
        return redirect('/sub_solution_package')->with('success', 'Berhasil mengubah data');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\SubSolutionPackage $subSolutionPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubSolutionPackage $subSolutionPackage)
    {
        try {
            SubSolutionPackage::destroy($subSolutionPackage->id);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors(['error', $th->getMessage()]);
        }

        return redirect()->back()->with('success', 'Berhasil menghapus data');
    }

    public function search(SubSolutionPackage $subSolutionPackage)
    {

        return SubSolutionPackage::where('id_solution_package', $subSolutionPackage->id_solution_package)->get();
    }

}
