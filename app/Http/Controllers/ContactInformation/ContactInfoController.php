<?php

namespace App\Http\Controllers\ContactInformation;

use App\Http\Controllers\ApiController;
use App\Models\ContactInfo\ContactInformation;
use App\Models\ContactInfo\ContactNumber;
use App\Models\ContactInfo\Email;
use App\Models\ContactInfo\InternetCommunication;
use App\Models\ContactInfo\PersonalLink;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function reset;
use function response;

class ContactInfoController extends ApiController
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    private function storeValidation($request){
        foreach($request->emails as $email) {
            $emailRequest = new Request($email);
            $this->validate($emailRequest,['email_address'=>'email']);
        }
        if($request->has('contact_numbers')){
            foreach($request->contact_numbers as $number) {
                $numberRequest = new Request($number);
                $this->validate($numberRequest,[

                    'country_code'=>'required',
                    'phone_number'=>'required',
                ]);
            }
        }
        if($request->has('internet_communications')){
            foreach($request->internet_communications as $account) {
                $accountRequest = new Request($account);
                $this->validate($accountRequest,[
                    'type'=>'required',
                    'address'=>'required',
                ]);
            }
        }
        if($request->has('personal_links')){
            foreach($request->personal_links as $account) {
                $accountRequest = new Request($account);
                $this->validate($accountRequest,[
                    'type'=>'required',
                    'url'=>'required',
                ]);
            }
        }

    }

    public function store(Request $request)
    {

        $this->validate($request, ['emails' => 'required', 'resume_id'=>'required|integer']);

        /*
         * If CV already has Contact Information we can't add another one
         * because the relation is one to one
        */

        $resume=Resume::findOrFail($request->resume_id);

        if($resume->contactInformation != null){
            return $this->errorResponse('Trying To Access Filled Field', 409);
        }

        $this->storeValidation($request);


        return DB::transaction(function() use ($request) {
            $contactInfo=ContactInformation::create(['resume_id'=>$request->resume_id]);

            foreach($request->emails as $email){
                Email::create([
                    'contact_information_id'=>$contactInfo->id,
                    'email_address'=>$email['email_address']
                ]);
            }


            if($request->has('contact_numbers')){

                foreach($request->contact_numbers as $number) {

                    ContactNumber::create([
                        'phone_type'=>$number['phone_type'],
                        'country_code'=>$number['country_code']['code'],
                        'phone_number'=>$number['phone_number'],
                        'contact_information_id'=>$contactInfo->id

                    ]);
                }
                
            }

//            return response()->json($request);

            if($request->has('internet_communications')){
                foreach($request->internet_communications as $account) {
                    InternetCommunication::create([
                        'type'=>$account['type'],
                        'address'=>$account['address'],
                        'contact_information_id'=>$contactInfo->id

                    ]);
                }
            }
            if($request->has('personal_links')){
                foreach($request->personal_links as $account) {
                    PersonalLink::create([
                        'type'=>$account['type'],
                        'url'=>$account['url'],
                        'contact_information_id'=>$contactInfo->id

                    ]);
                }
            }

            $contactInfo->emails;
            $contactInfo->contactNumbers;
            $contactInfo->internetCommunications;
            $contactInfo->personalLinks;

            return $this->showOne($contactInfo);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Resume $contactInfo)
    {


        if( $contactInfo->contactInformation ==null){
            return response()->json(['data' => 404]);
        }
        $contactInformationId= $contactInfo->contactInformation->id;
        $contactInformation=ContactInformation::where([['id','=',$contactInformationId]])
            ->with(['emails','contactNumbers','internetCommunications','personalLinks'])->get();

        return $this->showAll($contactInformation);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactInformation $contactInfo)
    {

        $this->validate($request, ['emails' => 'required']);
        $this->storeValidation($request);
        return DB::transaction(function() use ($request, $contactInfo) {

            if ($contactInfo->emails != null) {
                $contactInfo->emails()->delete();
            }
            foreach ($request->emails as $email) {
                Email::create([
                    'contact_information_id' => $contactInfo->id,
                    'email_address' => $email['email_address']
                ]);
            }

            if ($request->has('contact_numbers')) {
                if ($contactInfo->contactNumbers != null) {
                    $contactInfo->contactNumbers()->delete();
                }
                foreach ($request->contact_numbers as $number) {
                    ContactNumber::create([
                        'phone_type' => $number['phone_type'],
                        'country_code' => $number['country_code']['code'],
                        'phone_number' => $number['phone_number'],
                        'contact_information_id' => $contactInfo->id

                    ]);
                }
            }

            if ($request->has('internet_communications')) {
                if ($contactInfo->internetCommunications != null) {
                    $contactInfo->internetCommunications()->delete();
                }
                foreach ($request->internet_communications as $account) {
                    InternetCommunication::create([
                        'type' => $account['type'],
                        'address' => $account['address'],
                        'contact_information_id' => $contactInfo->id]);
                }
            }

            if ($request->has('personal_links')) {
                if ($contactInfo->personalLinks != null) {
                    $contactInfo->personalLinks()->delete();
                }
                foreach ($request->personal_links as $account) {
                    PersonalLink::create([
                        'type' => $account['type'],
                        'url' => $account['url'],
                        'contact_information_id' => $contactInfo->id]);
                }
            }


            $data = ContactInformation::where('id',$contactInfo->id)->
            with([
                'emails',
                'contactNumbers',
                'internetCommunications',
                'personalLinks',
            ])->get();
            return $this->showAll($data);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactInformation $contactInfo)
    {
        $contactInfo->delete();
        return $this->showOne($contactInfo);
    }
}
