public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->longText('signature')->nullable();
        $table->timestamp('signed_at')->nullable();
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['signature', 'signed_at']);
    });
}