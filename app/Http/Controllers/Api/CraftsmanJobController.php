<?php

namespace App\Http\Controllers\Api;

use App\Models\CraftsmanJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CraftsmanJobController extends Controller
{
    // Show all jobs for public
    public function jobs()
    {
        try {
            return response()->json([
                "jobs" => CraftsmanJob::all()->select('id', 'name', 'img_path', 'img_title', 'description')
            ], 200);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des métiers."
            ], 500);
        }
    }

    // Show all jobs for admin
    public function adminJobs()
    {
        try {
            return response()->json([
                "jobs" => CraftsmanJob::orderBy('id', 'desc')->get()
            ], 200);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des métiers."
            ], 500);
        }
    }

    // Show single job by id
    public function singleJob($id) 
    {
        try {

            return response()->json(CraftsmanJob::findOrFail($id));

        } catch (ModelNotFoundException $e) {

            return response()->json([
                "message" => "Métier inconnu."
            ], 404);

        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération du métier."
            ], 500);

        }
    }

    // Add single job
    public function addJob(Request $req) 
    {
        try {
            $req->validate([
                "name" => "required|string|max:255|unique:craftsman_jobs,name",
                "img_title" => "nullable|string|max:255",
                "image" => "nullable|image|max:3072|mimes:jpg,png,jpeg,webp",
                "description" => "nullable|string|max:5000"
            ], $this->messages());
            
            $newJobCat = CraftsmanJob::create([
                "name" => $req->name,
                "img_title" => $req->img_title ?? null,
                "img_path" => null,
                "description" => $req->description ?? null,
            ]);
            
            if($req->hasFile('image')) {
                $path = $req->image->store('/img/jobs', 'public');
                $newJobCat->img_path = $path;
                $newJobCat->save();
            }

            return response()->json([
                "message" => "Nouveau métier ajouté !",
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur est survenue lors de la création d'un nouveau métier."
            ], 500);
        }
    }

    // Update craftsman job
    public function updateJob(Request $req, $craftsmanJobId)
    {
        try {
            $jobCategory = CraftsmanJob::findOrFail($craftsmanJobId);
            $req->validate([
                "name" => "required|string|max:255|unique:craftsman_jobs,name," . $jobCategory->id,
                "img_title" => "nullable|string|max:255",
                "image" => "nullable|image|max:3072|mimes:jpg,png,jpeg,webp",
                "description" => "nullable|string|max:5000"
            ], $this->messages());

            $oldPath = $jobCategory->img_path ?? null;

            $updateData = [
                "name"        => $req->name,
                "img_title"   => $req->img_title ?? null,
                "description" => $req->description ?? null,
            ];

            if ($req->hasFile('image')) {
                if($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                
                $path = $req->image->store('/img/jobs', 'public');
                $updateData['img_path'] = $path;
            }

            if (!$req->hasFile('image') && $req->remove_img) {
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                $updateData['img_path'] = null;
            }

            $jobCategory->update($updateData);

            return response()->json([
                "message" => "Les informations du métier ont bien été mises à jour.",
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Throw this if craftsman job's id don't exist
            return response()->json([
                'message' => 'Métier non trouvé.',
            ], 404);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur est survenue lors de la mise à jour du métier.", 'a' => $e->getMessage()
            ], 500);
        }
    }

    // Delete craftsman job

    public function deleteJob($craftsmanJobId) {

        try {
            $jobCategory = CraftsmanJob::findOrFail($craftsmanJobId);
            $imgPath = $jobCategory->img_path;

            if($imgPath) {
                Storage::disk('public')->delete($imgPath);
            }

            $jobCategory->delete();

            return response()->json(["message" => "Le métier a été supprimer avec succès !"], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                "message" => "Métier non trouvée ou non autorisée."
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                "message" => "Une erreur est survenu lors de la supression du métier."
            ], 500);

        }
    }

    protected function messages() : array {
        return [
            "name.required" => "Veuillez renseigner le nom du métier.",
            "name.string" => "Le nom doit être une chaîne de caractères.",
            "name.max" => "Le nom ne peut pas dépasser 255 caractères.",
            "name.unique" => "Le métier que vous essayez d'ajouter existe déjà.",

            "img_title.string" => "Le nom doit être une chaîne de caractères.",
            "img_title.max" => "Le nom ne peut pas dépasser 255 caractères.",

            "image.image" => "Le fichier doit être une image.",
            "image.max" => "L'image ne peut pas dépasser 3 Mo.",
            "image.mimes" => "L'image doit être au format JPG, PNG, JPEG ou WEBP.",

            "description.string" => "La description doit être une chaîne de caractères.",
            "description.max" => "La description ne peut pas dépasser 5000 caractères.",
        ];
    }
}